<?php
namespace TDD;

use \BadMethodCallException;
use SebastianBergmann\ObjectEnumerator\Exception;

class Receipt
{
    public function subtotal(array $items = [], $coupon)
    {
        if ($coupon > 1.00) {
            throw new BadMethodCallException('Coupon must to be less than or equal to 1.00');
        }
        $sum = array_sum($items);
        if (!is_null($coupon)) {
            return $sum - $sum*$coupon;
        }
        return $sum;
    }

    public function tax($amount, $tax)
    {
        return $amount*$tax;
    }

    public function postTaxTotal($items, $tax, $coupon)
    {
        $subtotal = $this->subtotal($items, $coupon);
        return $subtotal + $this->tax($subtotal, $tax);
    }

    public function currencyAmt($input)
    {
        return round($input, 2);
    }
}
