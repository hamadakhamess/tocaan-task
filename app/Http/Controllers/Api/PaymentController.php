<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $orderId = $request->query('order_id');
        $payments = $this->paymentRepository->getPaginatedForOrder($orderId);

        return $this->successResponse($payments);
    }

    /**
     * Process a payment.
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            $payment = $this->paymentRepository->store($request->validated());
            return $this->successResponse($payment, 'Payment processed successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified payment.
     */
    public function show($id)
    {
        $payment = $this->paymentRepository->find($id);
        return $this->successResponse($payment);
    }
}
