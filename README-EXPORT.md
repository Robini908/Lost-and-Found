# Lost & Found System - Export Functionality

This document explains the bulk export functionality for lost and found items in the system.

## Features Implemented

### 1. Export Formats
- **PDF Export**: Generate professionally formatted PDF reports of lost/found items
- **Word Export**: Create Word (DOCX) documents for lost/found items
- **Print View**: A browser-friendly printable view with print button

### 2. User-specific Functionality
- **Regular Users**: Can export only their own reported items
- **Admins/Moderators**: Can export any/all items in the system

### 3. Bulk Selection
- Select multiple items for export
- Select all items functionality
- Clear selection functionality

### 4. Export Methods
- **Bulk Export**: Select multiple items to export through the main interface
- **User Dashboard Export**: Quick export of all user's items from their dashboard
- **Admin Export**: Full export capability from admin interface

## Technical Implementation

### Export Controller
- `ItemExportController`: Handles all export-related functionality
- Security checks to ensure users can only export their own items
- Admin-specific methods for system-wide exports

### Templates
- PDF export template with full styling
- Print-optimized view with browser print functionality
- Word document generation with proper formatting

### Routes
```php
// PDF Export Routes
Route::get('/items/export/pdf', [ItemExportController::class, 'exportPdf'])
    ->name('items.export.pdf');

// Word Export Routes
Route::get('/items/export/word', [ItemExportController::class, 'exportWord'])
    ->name('items.export.word');

// Print Routes
Route::get('/items/print', [ItemExportController::class, 'printItems'])
    ->name('items.print');

// User-specific exports
Route::get('/my-items/export/{format?}', [ItemExportController::class, 'exportMyItems'])
    ->name('my-items.export');

// Admin export all items
Route::middleware(['role:admin|superadmin|moderator'])->group(function () {
    Route::get('/admin/items/export/{format?}', [ItemExportController::class, 'exportAllItems'])
        ->name('admin.items.export');
});
```

## Usage

### For Regular Users

1. **From Item Listing:**
   - Select the checkboxes next to items you want to export
   - Use the bulk actions dropdown or the floating action bar at the bottom
   - Choose your export format (PDF, Word, or Print)

2. **From User Dashboard:**
   - Use the "Export PDF" or "Export Word" buttons at the top of "My Reported Items"

### For Admins

1. **From Admin Dashboard:**
   - Access the Items Management page
   - Use the export buttons at the top to export all items
   - Or use the bulk selection to export specific items

## Dependencies

- **mPDF**: For PDF generation (`mpdf/mpdf`)
- **PHPWord**: For Word document generation (`phpoffice/phpword`)

## Future Improvements

- Add CSV export functionality
- Add email functionality to send exports directly
- Enable scheduled/automated exports
- Add more customization options for exports 
