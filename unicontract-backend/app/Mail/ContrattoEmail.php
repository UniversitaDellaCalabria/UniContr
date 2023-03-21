<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\TitulusRef;
use App\Precontrattuale;
use Auth;

class ContrattoEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The pre instance.
     *
     * @var Order
     */
    protected $pre;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Precontrattuale $pre, $document, $documentName)
    {
        $this->pre = $pre;
        $this->document = $document;
        $this->documentName = $documentName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Contratto di insegnamento UniCal")
            ->markdown('emails.contrattomail')->with([
                'pre' => $this->pre,
                'titulus' => TitulusRef::where('insegn_id',$pre->insegn_id)->orderBy('created_at', 'desc')->first()
            ])
            ->attachData($this->document, $this->documentName, [
                'mime' => 'application/pdf',
            ]);
    }
}
