<!DOCTYPE html>
<html>
<head>
    <title>Membership Payment Pending</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; }
        .header { background-color: #f4f4f4; padding: 10px; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 0.9em; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Membership Payment Submitted</h1>
        </div>
        <div class="content">
            <p>Dear {{ $membership->user->name ?? 'Member' }},</p>

            <p>Thank you for submitting your payment for your <strong>{{ $membership->membershipType->name }}</strong> membership.</p>

            <p>Your payment details are as follows:</p>
            <ul>
                <li><strong>Membership Type:</strong> {{ $membership->membershipType->name }}</li>
                <li><strong>Amount Paid:</strong> {{ $payment->amount_paid }} {{ $payment->currency_code }}</li>
                <li><strong>Transaction ID (User Submitted):</strong> {{ $payment->manual_transaction_id_user }}</li>
                <li><strong>Payment Date & Time (User Submitted):</strong> {{ $payment->manual_payment_datetime_user->format('Y-m-d H:i A') }}</li>
                <li><strong>Payment Status:</strong> Awaiting Verification</li>
            </ul>

            <p>We have received your payment proof and it is currently pending manual verification by our team. You will be notified once your payment is confirmed and your membership is activated.</p>

            <p>If you have any questions, please don't hesitate to contact us.</p>

            <p>Sincerely,</p>
            <p>The {{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
