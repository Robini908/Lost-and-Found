<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactItemFounder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $founderName;
    public $requesterName;
    public $requesterEmail;
    public $itemTitle;
    public $location;
    public $dateFound;
    public $similarityScore;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->founderName = $data['founderName'];
        $this->requesterName = $data['requesterName'];
        $this->requesterEmail = $data['requesterEmail'];
        $this->itemTitle = $data['itemTitle'];
        $this->location = $data['location'];
        $this->dateFound = $data['dateFound'];
        $this->similarityScore = $data['similarityScore'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Item Match Confirmation - {$this->itemTitle}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-item-founder',
            with: [
                'founderName' => $this->founderName,
                'requesterName' => $this->requesterName,
                'requesterEmail' => $this->requesterEmail,
                'itemTitle' => $this->itemTitle,
                'location' => $this->location,
                'dateFound' => $this->dateFound,
                'similarityScore' => $this->similarityScore,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
