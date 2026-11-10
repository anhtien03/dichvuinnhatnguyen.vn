<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteConfirmationCustomerMail extends Mailable
{
    use Queueable, SerializesModels;

    public QuoteRequest $quote;

    public function __construct(QuoteRequest $quote)
    {
        $this->quote = $quote;
    }

    public function build()
    {
        return $this->subject('✅ Xưởng In Nhật Nguyên Đã Nhận Yêu Cầu Báo Giá #' . $this->quote->code)
                    ->view('emails.quote_confirmation_customer');
    }
}
