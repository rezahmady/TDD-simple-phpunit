<?php
namespace TDD\Test;

require(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .'autoload.php');

use PHPUnit\Framework\TestCase;
use TDD\Receipt;

class ReceiptTest extends TestCase
{
    public function setUp()
    {
        $this->Receipt = new Receipt();
    }

    public function tearDown()
    {
        unset($this->Receipt);
    }
    /**
     * @dataProvider providerTotal
     */
    public function testSubtotalotal($items, $expected)
    {
        $coupon = null;
        $output = $this->Receipt->subtotal($items, $coupon);
        $this->assertEquals(
            $expected,
            $output,
            "When summing the total shoud be equal {$expected}"
        );
    }
    public function providerTotal()
    {
        return [
            'ints totaling 16' => [[1,2,5,8] , 16],
            [[-1,2,5,8] , 14],
            [[2,5,8] , 15],
        ];
    }

    public function testTotalAndCopoun()
    {
        $input = [0,2,5,8];
        $coupon = 0.20;
        $output = $this->Receipt->subtotal($input, $coupon);
        $this->assertEquals(
            12,
            $output,
            'When summing the total shoud be equal 12'
        );
    }

    public function testTotalException()
    {
        $input = [0,2,5,8];
        $coupon = 1.20;
        $this->expectException('BadMethodCallException');
        $this->Receipt->subtotal($input, $coupon);
    }

    public function testPostTaxTotal()
    {
        //Arrange
        $items = [1,2,5,8];
        $coupon = null;
        $tax = 0.20;
        $Receipt = $this->getMockBuilder('TDD\Receipt')
            ->setMethods(['subtotal' , 'tax'])
            ->getMock();
        $Receipt->expects($this->once())
            ->method('subtotal')
            ->with($items, $coupon)
            ->will($this->returnValue(10.00));
        $Receipt->expects($this->once())
            ->method('tax')
            ->with(10.00, $tax)
            ->will($this->returnValue('1.00'));
        //Act
        $result = $Receipt->postTaxTotal($items, $tax, $coupon);
        //Assert
        $this->assertEquals('11.0', $result);
    }

    public function testTax()
    {
        $inputAmount = 10.00;
        $taxInput = 00.10;
        $output = $this->Receipt->tax($inputAmount, $taxInput);
        $this->assertEquals(
            1.00,
            $output,
            'The tax calculation shoud be equal 1.00'
        );
    }

    /**
     * @dataProvider    provideCurrencyAmt
     */
    public function testCurrencyAmt($items, $expected, $msg)
    {
        $this->assertSame(
            $expected,
            $this->Receipt->currencyAmt($items),
            $msg
        );
    }
    public function provideCurrencyAmt()
    {
        return [
            [1,1.00,'1 should be transform into 1.00'],
            [1.1,1.10,'1.1 should be transform into 1.10'],
            [1.11,1.11,'1.11 should be transform into 1.11'],
            [1.111,1.11,'1.111 should be transform into 1.11'],
        ];
    }
}
