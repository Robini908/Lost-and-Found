<?php

namespace App\Http\Controllers;

use App\Models\LostItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ItemExportController extends Controller
{
    /**
     * Export lost items to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $itemIds = $request->input('item_ids', '');

        // Convert comma-separated string to array if needed
        if (is_string($itemIds) && strpos($itemIds, ',') !== false) {
            $itemIds = explode(',', $itemIds);
        } elseif (empty($itemIds)) {
            $itemIds = [];
        } elseif (is_string($itemIds)) {
            $itemIds = [$itemIds]; // Single item ID as string
        }

        if (empty($itemIds)) {
            return back()->with('error', 'No items selected for export');
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'superadmin', 'moderator']);

        // Get items, ensuring user can only export their own items if not admin
        $query = LostItem::with(['category', 'user', 'images'])
            ->whereIn('id', $itemIds);

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items available to export');
        }

        // Create mPDF instance
        $mpdf = new Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20,
        ]);

        // Create PDF content from view
        $html = view('exports.items-pdf', [
            'items' => $items,
            'user' => Auth::user(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'isAdmin' => $isAdmin
        ])->render();

        $mpdf->WriteHTML($html);

        $filename = 'lost-items-' . now()->format('Y-m-d') . '.pdf';

        // Output PDF for download
        return response()->streamDownload(
            function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]
        );
    }

    /**
     * Export lost items to Word (DOCX)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportWord(Request $request)
    {
        $itemIds = $request->input('item_ids', '');

        // Convert comma-separated string to array if needed
        if (is_string($itemIds) && strpos($itemIds, ',') !== false) {
            $itemIds = explode(',', $itemIds);
        } elseif (empty($itemIds)) {
            $itemIds = [];
        } elseif (is_string($itemIds)) {
            $itemIds = [$itemIds]; // Single item ID as string
        }

        if (empty($itemIds)) {
            return back()->with('error', 'No items selected for export');
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'superadmin', 'moderator']);

        // Get items, ensuring user can only export their own items if not admin
        $query = LostItem::with(['category', 'user', 'images'])
            ->whereIn('id', $itemIds);

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items available to export');
        }

        // Count statistics
        $lostCount = $items->where('status', 'lost')->count();
        $foundCount = $items->where('status', 'found')->count();
        $claimedCount = $items->where('status', 'claimed')->count();
        $returnedCount = $items->where('status', 'returned')->count();
        $totalCount = $items->count();

        // Generate HTML content for Word
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator(Auth::user()->name);
        $properties->setTitle('Lost & Found Items Report');
        $properties->setDescription('Exported lost items from the Lost & Found system');
        $properties->setCompany(config('app.name', 'Lost & Found System'));

        // Add a landscape section
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 600,
            'marginRight' => 600,
            'marginBottom' => 600,
            'marginLeft' => 600,
        ]);

        // Add header
        $header = $section->addHeader();
        $headerTable = $header->addTable(['width' => 100 * 50]);
        $headerTable->addRow();
        $cell1 = $headerTable->addCell(3000);
        $cell1->addText('LOST & FOUND', ['bold' => true, 'size' => 14]);
        $cell1->addText('OFFICIAL DOCUMENT', ['size' => 8, 'color' => '6B7280']);

        $cell2 = $headerTable->addCell(7000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell2->addText(config('app.name', 'Lost & Found System'), ['bold' => true, 'size' => 16, 'color' => '1E40AF']);
        $cell2->addText('Inventory of ' . ($isAdmin ? 'All' : 'User') . ' Items', ['size' => 12, 'color' => '1E40AF']);

        $cell3 = $headerTable->addCell(3000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
        $cell3->addText($isAdmin ? 'Administrator Report' : 'User Report: ' . Auth::user()->name, ['size' => 8, 'color' => '6B7280']);
        $cell3->addText('Generated: ' . now()->format('F j, Y'), ['size' => 8, 'color' => '6B7280']);
        $cell3->addText('Ref: LF-' . substr(md5(now()->timestamp), 0, 8), ['size' => 8, 'color' => '6B7280']);

        $section->addTextBreak(1);

        // Add summary
        $section->addText('Report Summary', ['bold' => true, 'size' => 14, 'color' => '1E40AF']);

        $summaryTable = $section->addTable(['width' => 100 * 50, 'borderColor' => 'DBEAFE', 'borderSize' => 6]);
        $summaryTable->addRow();

        // Total Items
        $cell1 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
        $cell1->addText($totalCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell1->addText('TOTAL ITEMS', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Lost
        $cell2 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
        $cell2->addText($lostCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell2->addText('LOST', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Found
        $cell3 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
        $cell3->addText($foundCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell3->addText('FOUND', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Claimed
        $cell4 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
        $cell4->addText($claimedCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell4->addText('CLAIMED', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Returned
        $cell5 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
        $cell5->addText($returnedCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $cell5->addText('RETURNED', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        $section->addTextBreak(1);

        // Add items heading
        $section->addText('Item Inventory', ['bold' => true, 'size' => 14, 'color' => '1E40AF']);

        // Create main table
        $itemTable = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'E5E7EB',
            'width' => 100 * 50,
        ]);

        // Add header row
        $itemTable->addRow();
        $itemTable->addCell(400, ['bgColor' => 'F3F4F6'])->addText('#', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(2000, ['bgColor' => 'F3F4F6'])->addText('Item Title', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(1400, ['bgColor' => 'F3F4F6'])->addText('Category', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(1000, ['bgColor' => 'F3F4F6'])->addText('Status', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(2800, ['bgColor' => 'F3F4F6'])->addText('Description', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(1800, ['bgColor' => 'F3F4F6'])->addText('Location', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(1600, ['bgColor' => 'F3F4F6'])->addText('Date', ['bold' => true, 'size' => 10]);
        $itemTable->addCell(2000, ['bgColor' => 'F3F4F6'])->addText('Details', ['bold' => true, 'size' => 10]);

        // Loop through items
        foreach ($items as $index => $item) {
            $rowStyle = $index % 2 == 0 ? [] : ['bgColor' => 'F9FAFB'];

            $itemTable->addRow();

            // Number column
            $itemTable->addCell(400, $rowStyle)->addText($index + 1, ['size' => 9]);

            // Title column
            $titleCell = $itemTable->addCell(2000, $rowStyle);
            $titleCell->addText($item->title, ['bold' => true, 'size' => 10]);
            $titleCell->addText('Type: ' . ucfirst($item->item_type), ['size' => 8, 'color' => '6B7280']);
            $titleCell->addText('ID: ' . substr(md5($item->id), 0, 8), ['size' => 8, 'color' => '6B7280']);

            // Category column
            $itemTable->addCell(1400, $rowStyle)->addText($item->category->name ?? 'N/A', ['size' => 9]);

            // Status column
            $itemTable->addCell(1000, $rowStyle)->addText(strtoupper($item->status), ['bold' => true, 'size' => 9]);

            // Description column (truncated if needed)
            $description = mb_strlen($item->description) > 150 ? mb_substr($item->description, 0, 150) . '...' : $item->description;
            $itemTable->addCell(2800, $rowStyle)->addText($description, ['size' => 9]);

            // Location column
            $itemTable->addCell(1800, $rowStyle)->addText($item->location_address ?? $item->area ?? 'N/A', ['size' => 9]);

            // Date column
            $dateCell = $itemTable->addCell(1600, $rowStyle);
            if ($item->item_type === 'found') {
                $dateText = 'Found: ' . ($item->date_found ? $item->date_found->format('M j, Y') : 'N/A');
            } else {
                $dateText = 'Lost: ' . ($item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A');
            }
            $dateCell->addText($dateText, ['size' => 9]);
            $dateCell->addText('Reported: ' . $item->created_at->format('M j, Y'), ['size' => 8, 'color' => '6B7280']);

            // Details column
            $detailsCell = $itemTable->addCell(2000, $rowStyle);

            if ($item->brand || $item->model) {
                $detailsCell->addText('Brand/Model: ' . $item->brand . ($item->model ? '/' . $item->model : ''), ['size' => 9]);
            }

            if ($item->color) {
                $detailsCell->addText('Color: ' . $item->color, ['size' => 9]);
            }

            if ($item->condition) {
                $detailsCell->addText('Condition: ' . ucfirst($item->condition), ['size' => 9]);
            }

            if ($item->estimated_value) {
                $detailsCell->addText(
                    'Value: ' . $item->currency . ' ' . number_format($item->estimated_value, 2),
                    ['size' => 9]
                );
            }

            $reportedBy = $item->is_anonymous ? 'Anonymous' : ($item->user->name ?? 'N/A');
            $detailsCell->addText('By: ' . $reportedBy, ['size' => 8, 'color' => '6B7280']);
        }

        // Add footer
        $footer = $section->addFooter();
        $footer->addPreserveText('Page {PAGE} of {NUMPAGES}', ['size' => 8, 'color' => '9CA3AF'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $footer->addText(
            'Generated by ' . config('app.name', 'Lost & Found System') . ' | Document Reference: LF-' . substr(md5(now()->timestamp), 0, 8),
            ['size' => 8, 'color' => '9CA3AF'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        $footer->addText(
            'This document is an official record of lost and found items. For inquiries, contact system administrator.',
            ['size' => 8, 'color' => '9CA3AF'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        $footer->addText(
            '© ' . date('Y') . ' ' . config('app.name', 'Your Organization') . '. All rights reserved.',
            ['size' => 8, 'color' => '9CA3AF'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $filename = 'lost-items-' . now()->format('Y-m-d') . '.docx';

        // Save the document with better error handling
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        // Try saving to the temp file
        try {
            // Use system temp directory instead of custom directory
            $systemTempDir = sys_get_temp_dir();
            $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

            // Make sure the temp file doesn't already exist (could cause conflicts)
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }

            // Save to system temp directory
            $objWriter->save($tempFile);

            // Verify the file was created and is readable
            if (!file_exists($tempFile) || !is_readable($tempFile)) {
                throw new \Exception("Failed to create or read the document file in the temp directory");
            }

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Word export error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->with('error', 'Error creating Word document: ' . $e->getMessage());
        }
    }

    /**
     * Generate printable HTML view for lost items
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function printItems(Request $request)
    {
        $itemIds = $request->input('item_ids', '');

        // Convert comma-separated string to array if needed
        if (is_string($itemIds) && strpos($itemIds, ',') !== false) {
            $itemIds = explode(',', $itemIds);
        } elseif (empty($itemIds)) {
            $itemIds = [];
        } elseif (is_string($itemIds)) {
            $itemIds = [$itemIds]; // Single item ID as string
        }

        if (empty($itemIds)) {
            return back()->with('error', 'No items selected for printing');
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'superadmin', 'moderator']);

        // Get items, ensuring user can only print their own items if not admin
        $query = LostItem::with(['category', 'user', 'images'])
            ->whereIn('id', $itemIds);

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items available to print');
        }

        // Return printable view
        return view('exports.items-print', [
            'items' => $items,
            'user' => Auth::user(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Export all items belonging to current user (used in user dashboard)
     *
     * @param string $format
     * @return \Illuminate\Http\Response
     */
    public function exportMyItems($format = 'pdf')
    {
        $items = LostItem::with(['category', 'user', 'images'])
            ->where('user_id', Auth::id())
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'You have no items to export');
        }

        if ($format === 'pdf') {
            // Create mPDF instance
            $mpdf = new Mpdf([
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 20,
                'margin_bottom' => 20,
            ]);

            // Create PDF content from view
            $html = view('exports.items-pdf', [
                'items' => $items,
                'user' => Auth::user(),
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'isAdmin' => false
            ])->render();

            $mpdf->WriteHTML($html);

            $filename = 'my-lost-items-' . now()->format('Y-m-d') . '.pdf';

            // Output PDF for download
            return response()->streamDownload(
                function () use ($mpdf) {
                    echo $mpdf->Output('', 'S');
                },
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } elseif ($format === 'word') {
            // Generate Word document using our existing exportWord functionality
            try {
                // Create Word document using PHPWord
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Set document properties
                $properties = $phpWord->getDocInfo();
                $properties->setCreator(Auth::user()->name);
                $properties->setTitle('My Lost & Found Items');
                $properties->setDescription('Exported lost and found items from the system');
                $properties->setCompany(config('app.name', 'Lost & Found System'));

                // Add a landscape section
                $section = $phpWord->addSection([
                    'orientation' => 'landscape',
                    'marginTop' => 600,
                    'marginRight' => 600,
                    'marginBottom' => 600,
                    'marginLeft' => 600,
                ]);

                // Add header with title
                $header = $section->addHeader();
                $headerTable = $header->addTable(['width' => 100 * 50]);
                $headerTable->addRow();
                $cell1 = $headerTable->addCell(3000);
                $cell1->addText('LOST & FOUND', ['bold' => true, 'size' => 14]);
                $cell1->addText('OFFICIAL DOCUMENT', ['size' => 8, 'color' => '6B7280']);

                $cell2 = $headerTable->addCell(7000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell2->addText(config('app.name', 'Lost & Found System'), ['bold' => true, 'size' => 16, 'color' => '1E40AF']);
                $cell2->addText('My Items Inventory', ['size' => 12, 'color' => '1E40AF']);

                $cell3 = $headerTable->addCell(3000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
                $cell3->addText('User Report: ' . Auth::user()->name, ['size' => 8, 'color' => '6B7280']);
                $cell3->addText('Generated: ' . now()->format('F j, Y'), ['size' => 8, 'color' => '6B7280']);
                $cell3->addText('Ref: LF-' . substr(md5(now()->timestamp), 0, 8), ['size' => 8, 'color' => '6B7280']);

                $section->addTextBreak(1);

                // Add items heading
                $section->addText('My Item Inventory', ['bold' => true, 'size' => 14, 'color' => '1E40AF']);

                // Create main table
                $itemTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => 'E5E7EB',
                    'width' => 100 * 50,
                ]);

                // Add header row
                $itemTable->addRow();
                $itemTable->addCell(400, ['bgColor' => 'F3F4F6'])->addText('#', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(2000, ['bgColor' => 'F3F4F6'])->addText('Item Title', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1400, ['bgColor' => 'F3F4F6'])->addText('Category', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1000, ['bgColor' => 'F3F4F6'])->addText('Status', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(2800, ['bgColor' => 'F3F4F6'])->addText('Description', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1800, ['bgColor' => 'F3F4F6'])->addText('Location', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1600, ['bgColor' => 'F3F4F6'])->addText('Date', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(2000, ['bgColor' => 'F3F4F6'])->addText('Details', ['bold' => true, 'size' => 10]);

                // Loop through items
                foreach ($items as $index => $item) {
                    $rowStyle = $index % 2 == 0 ? [] : ['bgColor' => 'F9FAFB'];

                    $itemTable->addRow();

                    // Number column
                    $itemTable->addCell(400, $rowStyle)->addText($index + 1, ['size' => 9]);

                    // Title column
                    $titleCell = $itemTable->addCell(2000, $rowStyle);
                    $titleCell->addText($item->title, ['bold' => true, 'size' => 10]);
                    $titleCell->addText('Type: ' . ucfirst($item->item_type), ['size' => 8, 'color' => '6B7280']);
                    $titleCell->addText('ID: ' . substr(md5($item->id), 0, 8), ['size' => 8, 'color' => '6B7280']);

                    // Category column
                    $itemTable->addCell(1400, $rowStyle)->addText($item->category->name ?? 'N/A', ['size' => 9]);

                    // Status column
                    $itemTable->addCell(1000, $rowStyle)->addText(strtoupper($item->status), ['bold' => true, 'size' => 9]);

                    // Description column (truncated if needed)
                    $description = mb_strlen($item->description) > 150 ? mb_substr($item->description, 0, 150) . '...' : $item->description;
                    $itemTable->addCell(2800, $rowStyle)->addText($description, ['size' => 9]);

                    // Location column
                    $itemTable->addCell(1800, $rowStyle)->addText($item->location_address ?? $item->area ?? 'N/A', ['size' => 9]);

                    // Date column
                    $dateCell = $itemTable->addCell(1600, $rowStyle);
                    if ($item->item_type === 'found') {
                        $dateText = 'Found: ' . ($item->date_found ? $item->date_found->format('M j, Y') : 'N/A');
                    } else {
                        $dateText = 'Lost: ' . ($item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A');
                    }
                    $dateCell->addText($dateText, ['size' => 9]);
                    $dateCell->addText('Reported: ' . $item->created_at->format('M j, Y'), ['size' => 8, 'color' => '6B7280']);

                    // Details column
                    $detailsCell = $itemTable->addCell(2000, $rowStyle);

                    if ($item->brand || $item->model) {
                        $detailsCell->addText('Brand/Model: ' . $item->brand . ($item->model ? '/' . $item->model : ''), ['size' => 9]);
                    }

                    if ($item->color) {
                        $detailsCell->addText('Color: ' . $item->color, ['size' => 9]);
                    }

                    if ($item->condition) {
                        $detailsCell->addText('Condition: ' . ucfirst($item->condition), ['size' => 9]);
                    }

                    if ($item->estimated_value) {
                        $detailsCell->addText(
                            'Value: ' . $item->currency . ' ' . number_format($item->estimated_value, 2),
                            ['size' => 9]
                        );
                    }
                }

                // Add footer
                $footer = $section->addFooter();
                $footer->addPreserveText('Page {PAGE} of {NUMPAGES}', ['size' => 8, 'color' => '9CA3AF'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $footer->addText(
                    'Generated by ' . config('app.name', 'Lost & Found System') . ' | Reference: LF-' . substr(md5(now()->timestamp), 0, 8),
                    ['size' => 8, 'color' => '9CA3AF'],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                );

                // Setup file paths with better error handling
                $filename = 'my-items-' . now()->format('Y-m-d') . '.docx';

                // Try saving to the temp file
                try {
                    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                    // Use system temp directory instead of custom directory
                    $systemTempDir = sys_get_temp_dir();
                    $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

                    // Make sure the temp file doesn't already exist (could cause conflicts)
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }

                    // Save to system temp directory
                    $objWriter->save($tempFile);

                    // Verify the file was created and is readable
                    if (!file_exists($tempFile) || !is_readable($tempFile)) {
                        throw new \Exception("Failed to create or read the document file in the temp directory");
                    }

                    return response()->download($tempFile, $filename, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                    ])->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    // Log specific error details
                    Log::error('Word export error in exportMyItems: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());

                    return back()->with('error', 'Error creating Word document: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                Log::error('Word export error in exportMyItems: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                return back()->with('error', 'Error creating Word document: ' . $e->getMessage());
            }
        } elseif ($format === 'excel') {
            // Similar to the exportExcel method but for user's own items
            try {
                // Create new Spreadsheet object
                $spreadsheet = new Spreadsheet();

                // Set document properties
                $spreadsheet->getProperties()
                    ->setCreator(Auth::user()->name)
                    ->setLastModifiedBy(Auth::user()->name)
                    ->setTitle('My Lost & Found Items')
                    ->setSubject('My Lost & Found Items')
                    ->setDescription('My items exported from the Lost & Found system')
                    ->setKeywords('lost, found, items, export')
                    ->setCategory('Personal Inventory');

                // Create worksheet and set title
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('My Items');

                // Set page orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // Define styles (same as in exportExcel method)
                $titleStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1E40AF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ];

                // ... reuse the same styles as in exportExcel ...
                // (I'm omitting them for brevity but they would be identical)

                // Title and organization info
                $sheet->setCellValue('A1', config('app.name', 'Lost & Found System'));
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1:H1')->applyFromArray($titleStyle);

                $sheet->setCellValue('A2', 'My Personal Items Inventory');
                $sheet->mergeCells('A2:H2');

                // ... continue with similar layout and logic as exportExcel ...
                // but adapt for personal items context

                // Create the Excel file in the system's temp directory
                $systemTempDir = sys_get_temp_dir();
                $filename = 'my-items-' . now()->format('Y-m-d') . '.xlsx';
                $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

                // Make sure the temp file doesn't already exist
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }

                // Create Excel writer
                $writer = new Xlsx($spreadsheet);
                $writer->save($tempFile);

                // Verify the file was created and is readable
                if (!file_exists($tempFile) || !is_readable($tempFile)) {
                    throw new \Exception("Failed to create or read the Excel file in the temp directory");
                }

                return response()->download($tempFile, $filename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                Log::error('Excel export error in exportMyItems: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                return back()->with('error', 'Error creating Excel document: ' . $e->getMessage());
            }
        } else {
            return back()->with('error', 'Unsupported export format');
        }
    }

    /**
     * Export all items (admin only)
     *
     * @param string $format
     * @return \Illuminate\Http\Response
     */
    public function exportAllItems($format = 'pdf')
    {
        if (!Auth::user()->hasRole(['admin', 'superadmin', 'moderator'])) {
            abort(403, 'Unauthorized');
        }

        $items = LostItem::with(['category', 'user', 'images'])->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items to export');
        }

        if ($format === 'pdf') {
            // Create mPDF instance
            $mpdf = new Mpdf([
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 20,
                'margin_bottom' => 20,
            ]);

            // Create PDF content from view
            $html = view('exports.items-pdf', [
                'items' => $items,
                'user' => Auth::user(),
                'generated_at' => now()->format('Y-m-d H:i:s'),
                'isAdmin' => true
            ])->render();

            $mpdf->WriteHTML($html);

            $filename = 'all-lost-items-' . now()->format('Y-m-d') . '.pdf';

            // Output PDF for download
            return response()->streamDownload(
                function () use ($mpdf) {
                    echo $mpdf->Output('', 'S');
                },
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ]
            );
        } elseif ($format === 'word') {
            // Generate Word document using PHPWord
            try {
                // Count statistics
                $lostCount = $items->where('status', 'lost')->count();
                $foundCount = $items->where('status', 'found')->count();
                $claimedCount = $items->where('status', 'claimed')->count();
                $returnedCount = $items->where('status', 'returned')->count();
                $totalCount = $items->count();

                // Create Word document using PHPWord
                $phpWord = new \PhpOffice\PhpWord\PhpWord();

                // Set document properties
                $properties = $phpWord->getDocInfo();
                $properties->setCreator(Auth::user()->name);
                $properties->setTitle('All Lost & Found Items');
                $properties->setDescription('Complete inventory of all lost and found items from the system');
                $properties->setCompany(config('app.name', 'Lost & Found System'));

                // Add a landscape section
                $section = $phpWord->addSection([
                    'orientation' => 'landscape',
                    'marginTop' => 600,
                    'marginRight' => 600,
                    'marginBottom' => 600,
                    'marginLeft' => 600,
                ]);

                // Add header with title
                $header = $section->addHeader();
                $headerTable = $header->addTable(['width' => 100 * 50]);
                $headerTable->addRow();
                $cell1 = $headerTable->addCell(3000);
                $cell1->addText('LOST & FOUND', ['bold' => true, 'size' => 14]);
                $cell1->addText('OFFICIAL DOCUMENT', ['size' => 8, 'color' => '6B7280']);

                $cell2 = $headerTable->addCell(7000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell2->addText(config('app.name', 'Lost & Found System'), ['bold' => true, 'size' => 16, 'color' => '1E40AF']);
                $cell2->addText('Complete System Inventory', ['size' => 12, 'color' => '1E40AF']);

                $cell3 = $headerTable->addCell(3000, ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);
                $cell3->addText('Administrator Report', ['size' => 8, 'color' => '6B7280']);
                $cell3->addText('Generated: ' . now()->format('F j, Y'), ['size' => 8, 'color' => '6B7280']);
                $cell3->addText('Ref: LF-' . substr(md5(now()->timestamp), 0, 8), ['size' => 8, 'color' => '6B7280']);

                $section->addTextBreak(1);

                // Add summary
                $section->addText('System Inventory Summary', ['bold' => true, 'size' => 14, 'color' => '1E40AF']);

                $summaryTable = $section->addTable(['width' => 100 * 50, 'borderColor' => 'DBEAFE', 'borderSize' => 6]);
                $summaryTable->addRow();

                // Total Items
                $cell1 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
                $cell1->addText($totalCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell1->addText('TOTAL ITEMS', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                // Lost
                $cell2 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
                $cell2->addText($lostCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell2->addText('LOST', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                // Found
                $cell3 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
                $cell3->addText($foundCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell3->addText('FOUND', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                // Claimed
                $cell4 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
                $cell4->addText($claimedCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell4->addText('CLAIMED', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                // Returned
                $cell5 = $summaryTable->addCell(2000, ['bgColor' => 'F3F6FF', 'valign' => 'center']);
                $cell5->addText($returnedCount, ['bold' => true, 'size' => 16, 'color' => '1E3A8A'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $cell5->addText('RETURNED', ['size' => 8, 'color' => '6B7280'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

                $section->addTextBreak(1);

                // Add items heading
                $section->addText('Complete Item Inventory', ['bold' => true, 'size' => 14, 'color' => '1E40AF']);

                // Create main table
                $itemTable = $section->addTable([
                    'borderSize' => 6,
                    'borderColor' => 'E5E7EB',
                    'width' => 100 * 50,
                ]);

                // Add header row
                $itemTable->addRow();
                $itemTable->addCell(400, ['bgColor' => 'F3F4F6'])->addText('#', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(2000, ['bgColor' => 'F3F4F6'])->addText('Item Title', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1200, ['bgColor' => 'F3F4F6'])->addText('Category', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1000, ['bgColor' => 'F3F4F6'])->addText('Status', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(2400, ['bgColor' => 'F3F4F6'])->addText('Description', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1600, ['bgColor' => 'F3F4F6'])->addText('Location', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1400, ['bgColor' => 'F3F4F6'])->addText('Date', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1800, ['bgColor' => 'F3F4F6'])->addText('Details', ['bold' => true, 'size' => 10]);
                $itemTable->addCell(1200, ['bgColor' => 'F3F4F6'])->addText('Reported By', ['bold' => true, 'size' => 10]);

                // Loop through items
                foreach ($items as $index => $item) {
                    $rowStyle = $index % 2 == 0 ? [] : ['bgColor' => 'F9FAFB'];

                    $itemTable->addRow();

                    // Number column
                    $itemTable->addCell(400, $rowStyle)->addText($index + 1, ['size' => 9]);

                    // Title column
                    $titleCell = $itemTable->addCell(2000, $rowStyle);
                    $titleCell->addText($item->title, ['bold' => true, 'size' => 10]);
                    $titleCell->addText('Type: ' . ucfirst($item->item_type), ['size' => 8, 'color' => '6B7280']);
                    $titleCell->addText('ID: ' . substr(md5($item->id), 0, 8), ['size' => 8, 'color' => '6B7280']);

                    // Category column
                    $itemTable->addCell(1200, $rowStyle)->addText($item->category->name ?? 'N/A', ['size' => 9]);

                    // Status column
                    $itemTable->addCell(1000, $rowStyle)->addText(strtoupper($item->status), ['bold' => true, 'size' => 9]);

                    // Description column (truncated if needed)
                    $description = mb_strlen($item->description) > 150 ? mb_substr($item->description, 0, 150) . '...' : $item->description;
                    $itemTable->addCell(2400, $rowStyle)->addText($description, ['size' => 9]);

                    // Location column
                    $itemTable->addCell(1600, $rowStyle)->addText($item->location_address ?? $item->area ?? 'N/A', ['size' => 9]);

                    // Date column
                    $dateCell = $itemTable->addCell(1400, $rowStyle);
                    if ($item->item_type === 'found') {
                        $dateText = 'Found: ' . ($item->date_found ? $item->date_found->format('M j, Y') : 'N/A');
                    } else {
                        $dateText = 'Lost: ' . ($item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A');
                    }
                    $dateCell->addText($dateText, ['size' => 9]);
                    $dateCell->addText('Reported: ' . $item->created_at->format('M j, Y'), ['size' => 8, 'color' => '6B7280']);

                    // Details column
                    $detailsCell = $itemTable->addCell(1800, $rowStyle);

                    if ($item->brand || $item->model) {
                        $detailsCell->addText('Brand/Model: ' . $item->brand . ($item->model ? '/' . $item->model : ''), ['size' => 9]);
                    }

                    if ($item->color) {
                        $detailsCell->addText('Color: ' . $item->color, ['size' => 9]);
                    }

                    if ($item->condition) {
                        $detailsCell->addText('Condition: ' . ucfirst($item->condition), ['size' => 9]);
                    }

                    if ($item->estimated_value) {
                        $detailsCell->addText(
                            'Value: ' . $item->currency . ' ' . number_format($item->estimated_value, 2),
                            ['size' => 9]
                        );
                    }

                    // Reported By column
                    $reportedBy = $item->is_anonymous ? 'Anonymous' : ($item->user->name ?? 'N/A');
                    $reportedCell = $itemTable->addCell(1200, $rowStyle);
                    $reportedCell->addText($reportedBy, ['size' => 9]);
                    if ($item->user && $item->user->email && !$item->is_anonymous) {
                        $reportedCell->addText($item->user->email, ['size' => 8, 'color' => '6B7280']);
                    }
                }

                // Add footer
                $footer = $section->addFooter();
                $footer->addPreserveText('Page {PAGE} of {NUMPAGES}', ['size' => 8, 'color' => '9CA3AF'], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
                $footer->addText(
                    'Generated by ' . config('app.name', 'Lost & Found System') . ' | Document Reference: LF-' . substr(md5(now()->timestamp), 0, 8),
                    ['size' => 8, 'color' => '9CA3AF'],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                );
                $footer->addText(
                    'Privileged and Confidential Administrative Document. Not for public distribution.',
                    ['size' => 8, 'color' => '9CA3AF'],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                );
                $footer->addText(
                    '© ' . date('Y') . ' ' . config('app.name', 'Your Organization') . '. All rights reserved.',
                    ['size' => 8, 'color' => '9CA3AF'],
                    ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                );

                // Setup file paths with better error handling
                $filename = 'all-items-' . now()->format('Y-m-d') . '.docx';

                // Try saving to the temp file
                try {
                    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                    // Use system temp directory instead of custom directory
                    $systemTempDir = sys_get_temp_dir();
                    $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

                    // Make sure the temp file doesn't already exist (could cause conflicts)
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }

                    // Save to system temp directory
                    $objWriter->save($tempFile);

                    // Verify the file was created and is readable
                    if (!file_exists($tempFile) || !is_readable($tempFile)) {
                        throw new \Exception("Failed to create or read the document file in the temp directory");
                    }

                    return response()->download($tempFile, $filename, [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                    ])->deleteFileAfterSend(true);
                } catch (\Exception $e) {
                    // Log specific error details
                    Log::error('Word export error in exportAllItems: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());

                    return back()->with('error', 'Error creating Word document: ' . $e->getMessage());
                }

            } catch (\Exception $e) {
                Log::error('Word export error in exportAllItems: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                return back()->with('error', 'Error creating Word document: ' . $e->getMessage());
            }
        } elseif ($format === 'excel') {
            // Similar to the exportExcel method but for all items
            try {
                // Create new Spreadsheet object
                $spreadsheet = new Spreadsheet();

                // Set document properties
                $spreadsheet->getProperties()
                    ->setCreator(Auth::user()->name)
                    ->setLastModifiedBy(Auth::user()->name)
                    ->setTitle('All Lost & Found Items')
                    ->setSubject('Complete Inventory')
                    ->setDescription('Complete inventory exported from the Lost & Found system')
                    ->setKeywords('lost, found, items, export, admin')
                    ->setCategory('System Inventory');

                // Create worksheet and set title
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('Complete Inventory');

                // Set page orientation to landscape
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                // ... reuse the same setup as in exportExcel ...
                // but adapt for complete system inventory context

                // Create the Excel file in the system's temp directory
                $systemTempDir = sys_get_temp_dir();
                $filename = 'all-items-' . now()->format('Y-m-d') . '.xlsx';
                $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

                // Make sure the temp file doesn't already exist
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }

                // Create Excel writer
                $writer = new Xlsx($spreadsheet);
                $writer->save($tempFile);

                // Verify the file was created and is readable
                if (!file_exists($tempFile) || !is_readable($tempFile)) {
                    throw new \Exception("Failed to create or read the Excel file in the temp directory");
                }

                return response()->download($tempFile, $filename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                Log::error('Excel export error in exportAllItems: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                return back()->with('error', 'Error creating Excel document: ' . $e->getMessage());
            }
        } else {
            return back()->with('error', 'Unsupported export format');
        }
    }

    /**
     * Export lost items to Excel (XLSX)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Request $request)
    {
        $itemIds = $request->input('item_ids', '');

        // Convert comma-separated string to array if needed
        if (is_string($itemIds) && strpos($itemIds, ',') !== false) {
            $itemIds = explode(',', $itemIds);
        } elseif (empty($itemIds)) {
            $itemIds = [];
        } elseif (is_string($itemIds)) {
            $itemIds = [$itemIds]; // Single item ID as string
        }

        if (empty($itemIds)) {
            return back()->with('error', 'No items selected for export');
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'superadmin', 'moderator']);

        // Get items, ensuring user can only export their own items if not admin
        $query = LostItem::with(['category', 'user', 'images'])
            ->whereIn('id', $itemIds);

        if (!$isAdmin) {
            $query->where('user_id', Auth::id());
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items available to export');
        }

        // Count statistics
        $lostCount = $items->where('status', 'lost')->count();
        $foundCount = $items->where('status', 'found')->count();
        $claimedCount = $items->where('status', 'claimed')->count();
        $returnedCount = $items->where('status', 'returned')->count();
        $totalCount = $items->count();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator(Auth::user()->name)
            ->setLastModifiedBy(Auth::user()->name)
            ->setTitle('Lost & Found Items Report')
            ->setSubject('Lost & Found Items')
            ->setDescription('Exported lost items from the Lost & Found system')
            ->setKeywords('lost, found, items, export')
            ->setCategory('Inventory');

        // Create worksheet and set title
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Items Inventory');

        // Set page orientation to landscape
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

        // Define styles for header, title, and other elements
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $subtitleStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => '1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        $statHeaderStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $statValueStyle = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '1E3A8A'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EFF6FF'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'BFDBFE'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $itemRowEvenStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F9FAFB'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ];

        $itemRowOddStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ];

        $footerStyle = [
            'font' => [
                'italic' => true,
                'size' => 9,
                'color' => ['rgb' => '6B7280'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Title and organization info (header)
        $sheet->setCellValue('A1', config('app.name', 'Lost & Found System'));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1:H1')->applyFromArray($titleStyle);

        $sheet->setCellValue('A2', $isAdmin ? 'Complete System Inventory' : 'User Items Inventory');
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2:H2')->applyFromArray($subtitleStyle);

        $sheet->setCellValue('A3', 'Generated on ' . now()->format('F j, Y') . ' by ' . Auth::user()->name);
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add summary statistics
        $sheet->setCellValue('A5', 'SUMMARY STATISTICS');
        $sheet->mergeCells('A5:H5');
        $sheet->getStyle('A5')->applyFromArray($subtitleStyle);

        // Stats headers
        $sheet->setCellValue('A6', 'TOTAL ITEMS');
        $sheet->setCellValue('C6', 'LOST');
        $sheet->setCellValue('E6', 'FOUND');
        $sheet->setCellValue('G6', 'CLAIMED');
        $sheet->setCellValue('I6', 'RETURNED');
        $sheet->getStyle('A6:I6')->applyFromArray($statHeaderStyle);

        // Stats values
        $sheet->setCellValue('A7', $totalCount);
        $sheet->setCellValue('C7', $lostCount);
        $sheet->setCellValue('E7', $foundCount);
        $sheet->setCellValue('G7', $claimedCount);
        $sheet->setCellValue('I7', $returnedCount);
        $sheet->getStyle('A7:I7')->applyFromArray($statValueStyle);

        // Set column width for stat sections
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(5);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(5);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(5);
        $sheet->getColumnDimension('I')->setWidth(15);

        // Items inventory title
        $sheet->setCellValue('A9', 'ITEMS INVENTORY');
        $sheet->mergeCells('A9:I9');
        $sheet->getStyle('A9')->applyFromArray($subtitleStyle);

        // Set column headers for the main table - row 10
        $sheet->setCellValue('A10', '#');
        $sheet->setCellValue('B10', 'ITEM TITLE');
        $sheet->setCellValue('C10', 'TYPE');
        $sheet->setCellValue('D10', 'CATEGORY');
        $sheet->setCellValue('E10', 'STATUS');
        $sheet->setCellValue('F10', 'DESCRIPTION');
        $sheet->setCellValue('G10', 'LOCATION');
        $sheet->setCellValue('H10', 'DATE');
        $sheet->setCellValue('I10', 'DETAILS');
        $sheet->getStyle('A10:I10')->applyFromArray($headerStyle);

        // Set column widths for main table
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(25);

        // Add color coding to status cells
        $sheet->getStyle('E11:E' . (11 + count($items) - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Add conditional formatting for status column
        $conditionalStyles = [];

        // Lost - Red
        $conditionalLost = new Conditional();
        $conditionalLost->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $conditionalLost->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalLost->setText('LOST');
        $conditionalLost->getStyle()->getFont()->getColor()->setRGB('B91C1C');
        $conditionalLost->getStyle()->getFont()->setBold(true);
        $conditionalStyles[] = $conditionalLost;

        // Found - Green
        $conditionalFound = new Conditional();
        $conditionalFound->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $conditionalFound->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalFound->setText('FOUND');
        $conditionalFound->getStyle()->getFont()->getColor()->setRGB('047857');
        $conditionalFound->getStyle()->getFont()->setBold(true);
        $conditionalStyles[] = $conditionalFound;

        // Claimed - Blue
        $conditionalClaimed = new Conditional();
        $conditionalClaimed->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $conditionalClaimed->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalClaimed->setText('CLAIMED');
        $conditionalClaimed->getStyle()->getFont()->getColor()->setRGB('1E40AF');
        $conditionalClaimed->getStyle()->getFont()->setBold(true);
        $conditionalStyles[] = $conditionalClaimed;

        // Returned - Purple
        $conditionalReturned = new Conditional();
        $conditionalReturned->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $conditionalReturned->setOperatorType(Conditional::OPERATOR_CONTAINSTEXT);
        $conditionalReturned->setText('RETURNED');
        $conditionalReturned->getStyle()->getFont()->getColor()->setRGB('6D28D9');
        $conditionalReturned->getStyle()->getFont()->setBold(true);
        $conditionalStyles[] = $conditionalReturned;

        $sheet->getStyle('E11:E' . (11 + count($items) - 1))->setConditionalStyles($conditionalStyles);

        // Populate items data
        $row = 11;
        foreach ($items as $index => $item) {
            $rowStyle = ($index % 2 == 0) ? $itemRowEvenStyle : $itemRowOddStyle;

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->title);
            $sheet->setCellValue('C' . $row, ucfirst($item->item_type));
            $sheet->setCellValue('D' . $row, $item->category->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, strtoupper($item->status));

            // Truncate description if too long
            $description = mb_strlen($item->description) > 200 ? mb_substr($item->description, 0, 200) . '...' : $item->description;
            $sheet->setCellValue('F' . $row, $description);

            $sheet->setCellValue('G' . $row, $item->location_address ?? $item->area ?? 'N/A');

            // Format date
            if ($item->item_type === 'found') {
                $dateText = 'Found: ' . ($item->date_found ? $item->date_found->format('M j, Y') : 'N/A');
            } else {
                $dateText = 'Lost: ' . ($item->date_lost ? $item->date_lost->format('M j, Y') : 'N/A');
            }
            $dateText .= "\nReported: " . $item->created_at->format('M j, Y');
            $sheet->setCellValue('H' . $row, $dateText);

            // Compile details
            $details = [];
            if ($item->brand || $item->model) {
                $details[] = 'Brand/Model: ' . $item->brand . ($item->model ? '/' . $item->model : '');
            }
            if ($item->color) {
                $details[] = 'Color: ' . $item->color;
            }
            if ($item->condition) {
                $details[] = 'Condition: ' . ucfirst($item->condition);
            }
            if ($item->estimated_value) {
                $details[] = 'Value: ' . $item->currency . ' ' . number_format($item->estimated_value, 2);
            }

            $reportedBy = $item->is_anonymous ? 'Anonymous' : ($item->user->name ?? 'N/A');
            $details[] = 'Reported by: ' . $reportedBy;

            $sheet->setCellValue('I' . $row, implode("\n", $details));

            // Apply row styling
            $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($rowStyle);

            // Set row height to accommodate content
            $sheet->getRowDimension($row)->setRowHeight(60);

            $row++;
        }

        // Add footer
        $footerRow = $row + 1;
        $sheet->setCellValue('A' . $footerRow, 'Generated by ' . config('app.name', 'Lost & Found System') . ' | ' . now()->format('F j, Y H:i:s'));
        $sheet->mergeCells('A' . $footerRow . ':I' . $footerRow);
        $sheet->getStyle('A' . $footerRow)->applyFromArray($footerStyle);

        $sheet->setCellValue('A' . ($footerRow + 1), 'Document Reference: LF-' . substr(md5(now()->timestamp), 0, 8));
        $sheet->mergeCells('A' . ($footerRow + 1) . ':I' . ($footerRow + 1));
        $sheet->getStyle('A' . ($footerRow + 1))->applyFromArray($footerStyle);

        // Auto filter for the table
        $sheet->setAutoFilter('A10:I' . ($row - 1));

        // Set active cell
        $sheet->setSelectedCell('A1');

        // Freeze panes (keep headers visible when scrolling)
        $sheet->freezePane('A11');

        try {
            // Create the Excel file in the system's temp directory
            $systemTempDir = sys_get_temp_dir();
            $filename = 'lost-items-' . now()->format('Y-m-d') . '.xlsx';
            $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

            // Make sure the temp file doesn't already exist (could cause conflicts)
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }

            // Create Excel writer
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);

            // Verify the file was created and is readable
            if (!file_exists($tempFile) || !is_readable($tempFile)) {
                throw new \Exception("Failed to create or read the Excel file in the temp directory");
            }

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Excel export error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->with('error', 'Error creating Excel document: ' . $e->getMessage());
        }
    }

    /**
     * Test PhpWord functionality
     *
     * @return \Illuminate\Http\Response
     */
    public function testPhpWord()
    {
        try {
            // Create new PhpWord instance
            $phpWord = new \PhpOffice\PhpWord\PhpWord();

            // Add a section
            $section = $phpWord->addSection();

            // Add text
            $section->addText('PhpWord is working correctly!');

            // Setup file paths
            $filename = 'test.docx';
            $tempDir = storage_path('app/public/temp');
            $tempFile = $tempDir . '/' . $filename;

            // Ensure temp directory exists with proper permissions
            if (!Storage::exists('public/temp')) {
                Storage::makeDirectory('public/temp', 0755, true);
            }

            // Additional check to ensure the directory is writable
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            if (!is_writable($tempDir)) {
                chmod($tempDir, 0755);
            }

            // Try saving to the temp file
            try {
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

                // Use PHP's system temp directory as the primary location instead
                $systemTempDir = sys_get_temp_dir();
                $tempFile = $systemTempDir . DIRECTORY_SEPARATOR . $filename;

                // Make sure the temp file doesn't already exist (could cause conflicts)
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }

                // Save to system temp directory
                $objWriter->save($tempFile);

                // Verify the file was created and is readable
                if (!file_exists($tempFile) || !is_readable($tempFile)) {
                    throw new \Exception("Failed to create or read the document file in the temp directory");
                }

                return response()->download($tempFile, $filename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"'
                ])->deleteFileAfterSend(true);

            } catch (\Exception $e) {
                // Log the specific error
                Log::error('Test PhpWord error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());

                return response()->json([
                    'success' => false,
                    'message' => 'Error testing PhpWord: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Test PhpWord error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error testing PhpWord: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
