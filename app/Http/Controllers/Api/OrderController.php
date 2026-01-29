<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $orders = $this->orderRepository->getPaginatedWithStatus($status);

        return $this->successResponse($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderRepository->store($request->all());
        return $this->successResponse($order, 'Order created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = $this->orderRepository->find($id)->load('items', 'payments');
        return $this->successResponse($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->orderRepository->update($id, $request->validated());
        return $this->successResponse($order->load('items'), 'Order updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->find($id);

        if ($order->payments()->exists()) {
            return $this->errorResponse('Cannot delete order with associated payments.', 400);
        }

        $this->orderRepository->delete($id);
        return $this->successResponse(null, 'Order deleted successfully.');
    }
}
