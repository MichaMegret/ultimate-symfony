<?php

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Repository\PurchaseRepository;
use Doctrine\Persistence\ManagerRegistry;

$a=new PurchaseRepository(new ManagerRegistry);
dd($a);
\Stripe\Stripe::setApiKey('sk_test_51IeK8BHHSlDHee2UD0DTS2RzBXxAdSM45Vdl4RDX4FwRseHCnnMGqKQ5O4ZURsQhywDo0J21wA1H3BdzmtxxS6HY00p2sDmp86');
$payload = @file_get_contents('php://input');
$event = null;
try {
  $event = \Stripe\Event::constructFrom(
    json_decode($payload, true)
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  echo '⚠️  Webhook error while parsing basic request.';
  http_response_code(400);
  exit();
}
// Handle the event
switch ($event->type) {
  case 'payment_intent.succeeded':
    $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
    // Then define and call a method to handle the successful payment intent.
    // handlePaymentIntentSucceeded($paymentIntent);
    break;
  case 'payment_method.attached':
    $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
    // Then define and call a method to handle the successful attachment of a PaymentMethod.
    // handlePaymentMethodAttached($paymentMethod);
    break;
  default:
    // Unexpected event type
    echo 'Received unknown event type';
}
http_response_code(200);