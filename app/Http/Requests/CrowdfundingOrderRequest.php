<?php

namespace App\Http\Requests;

use App\Models\CrowdfundingProduct;
use App\Models\Product;
use App\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrowdfundingOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku_id'     => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('该商品不存在');
                    }
                    if ($sku->product->type !== Product::TYPE_CROWDFUNDING) {
                        return $fail('该商品不支持众筹');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('该商品未上架');
                    }

                    if ($sku->stock === 0) {
                        return $fail('该商品已售完');
                    }
                    // 还需要判断众筹本身的状态，如果不是众筹中则无法下单
                    if ($sku->product->crowdfunding->end_at < Carbon::now() || $sku->product->crowdfunding->status !== CrowdfundingProduct::STATUS_FUNDING) {
                        return $fail('该商品众筹已结束');
                    }

                    if ($this->input('amount') > 0 && $this->input('amount') > $sku->stock) {
                        return $fail('该商品库存不足');
                    }
                }
            ],
            'address_id' => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
            'amount'     => ['required', 'integer', 'min:1'],
        ];
    }
}
