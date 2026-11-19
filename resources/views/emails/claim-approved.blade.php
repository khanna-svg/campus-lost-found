<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Approved</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f4f5; margin: 0; padding: 20px; color: #18181b; }
        .container { max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e4e4e7; overflow: hidden; }
        .header { background-color: #000000; color: #ffffff; padding: 28px 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .body { padding: 32px 24px; }
        .greeting { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
        .lead { font-size: 14px; line-height: 1.6; color: #3f3f46; margin-bottom: 24px; }
        .pickup-box { background-color: #fafafa; border: 1px solid #e4e4e7; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .pickup-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #71717a; margin-bottom: 12px; }
        .pickup-item { margin-bottom: 10px; font-size: 13px; line-height: 1.5; }
        .pickup-item strong { color: #18181b; }
        .badge { display: inline-block; background-color: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; text-transform: uppercase; margin-bottom: 16px; }
        .footer { padding: 20px 24px; background-color: #fafafa; border-top: 1px solid #f4f4f5; text-align: center; font-size: 12px; color: #a1a1aa; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>CampusBeacon</h1>
        </div>
        <div class="body">
            <span class="badge">Claim Approved · Ready for Pickup</span>
            <div class="greeting">Hello {{ $claim->claimant->name }},</div>
            <div class="lead">
                Great news! Your ownership claim request for <strong>"{{ $claim->foundItem->item_name }}"</strong> has been verified and <strong>APPROVED</strong> by the campus administration. Your item is now safely secured and ready for you to retrieve.
            </div>

            <div class="pickup-box">
                <div class="pickup-title">Office Retrieval Details</div>
                <div class="pickup-item">
                    <strong>Pickup Location:</strong> Admin Office
                </div>
                <div class="pickup-item">
                    <strong>Office Hours:</strong> Monday – Friday, 8:00 AM – 5:00 PM
                </div>
                <div class="pickup-item">
                    <strong>Required Document:</strong> Please bring your valid <strong>School ID</strong> to confirm your identity.
                </div>
                @if ($claim->pickup_instructions)
                    <div class="pickup-item" style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #e4e4e7;">
                        <strong>Administrator Note:</strong> {{ $claim->pickup_instructions }}
                    </div>
                @endif
            </div>

            <p style="font-size: 13px; color: #71717a; line-height: 1.5; margin: 0;">
                If you have any questions or are unable to pick up your item during regular hours, please contact the Lost &amp; Found office directly.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CampusBeacon Central Portal. All rights reserved.
        </div>
    </div>
</body>
</html>
