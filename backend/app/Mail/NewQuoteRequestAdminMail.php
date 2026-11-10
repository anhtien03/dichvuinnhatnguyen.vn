<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewQuoteRequestAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public QuoteRequest $quote;

    public function __construct(QuoteRequest $quote)
    {
        $this->quote = $quote;
    }

    public function build()
    {
        return $this->subject('🔔 [Đơn Mới] Yêu Cầu Báo Giá In Ấn #' . $this->quote->code . ' - ' . $this->quote->customer_name)
                    ->view('emails.new_quote_admin');
    }
}

