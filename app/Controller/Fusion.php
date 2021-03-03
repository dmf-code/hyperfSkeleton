<?php


namespace App\Controller;


use Carbon\Carbon;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Paginator\LengthAwarePaginator;
use Hyperf\Paginator\Paginator;
use Hyperf\Utils\ApplicationContext;


trait Fusion
{

    public function inputs($params): array
    {
        return array_combine($params, $this->request->inputs($params));
    }

    protected function success($msg="ok", $data = [], $code=200): array
    {

        return resp($code, $msg, $data);
    }

    protected function failed($code=400, $msg='', $data=[]): array
    {
        return resp($code, $msg, $data);
    }

    protected function formatPaginator(LengthAwarePaginatorInterface $paginator): array
    {
        return [
            'items'=>$paginator->items(),
            'total'=>$paginator->total(),
            'current_page'=>$paginator->currentPage(),
            'per_page'=>$paginator->perPage(),
        ];
    }

    protected function customPaginator($list,$perPage = 10, $isSlice = true, $total = null): array
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);

        $current_page = $request->input('page', 1);
        $current_page = $current_page <= 0 ? 1 : $current_page;

        if ($isSlice) {
            $item = array_slice($list, ($current_page - 1) * $perPage, $perPage);//$Array为要分页的数组
        } else {
            $item = $list;
        }

        $totals = $total;
        if (is_null($total)) {
            $totals = count($list);
        }

        $paginator = new LengthAwarePaginator($item, $totals, $perPage, $current_page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
        return $this->formatPaginator($paginator);
    }

        public function validator($rules, $message = []): ?string
    {
        $validator = $this->validationFactory
            ->make($this->request->all(), $rules, $message);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        return null;
    }

    public function equal($model, $key, $field=null)
    {
        if (!is_null($item = $this->request->input($key, null))) {
            if (is_null($field)) {
                $field = $key;
            }
            $model->where([$field => $item]);
        }
    }

    public function in($model, $key, $field=null)
    {
        if (!is_null($item = $this->request->input($key, null))) {
            if (is_null($field)) {
                $field = $key;
            }
            $model->whereIn($field, explode(",", $item));
        }
    }

    public function like($model, $key, $field=null, $format=null)
    {
        if (!is_null($item = $this->request->input($key, null))) {
            if (is_null($field)) {
                $field = $key;
            }
            if (!is_null($format)) {
                $item = sprintf($format, $item);
            }
            $model->where($field, 'like', $item);
        }
    }

    public function date($model, $key = 'date', $field='created_at')
    {
        if (!is_null($date = $this->request->input($key, null))) {
            switch ($date) {
                case 'day':
                    $model->whereDate($field, Carbon::today()->toDateString());
                    break;
                case '-day':
                    $model->whereDate($field, Carbon::yesterday()->toDateString());
                    break;
                case '-7days':
                    $model->whereDate($field, '>=', Carbon::parse('-7 days')->toDateString())
                    ->whereDate($field, '<=', Carbon::now()->toDateString());
                    break;
                case '-15days':
                    $model->whereDate($field, '>=', Carbon::parse('-15 days')->toDateString())
                        ->whereDate($field, '<=', Carbon::now()->toDateString());
                    break;
                case '-30days':
                    $model->whereDate($field, '>=', Carbon::parse('-30 days')->toDateString())
                        ->whereDate($field, '<=', Carbon::now()->toDateString());
                    break;
                case '-1/2year':
                    $model->whereDate($field, '>=', Carbon::parse('-6 months')->toDateString())
                        ->whereDate($field, '<=', Carbon::now()->toDateString());
                    break;
                case '-year':
                    $model->whereDate($field, '>=', Carbon::parse('-1 year')->toDateString())
                        ->whereDate($field, '<=', Carbon::now()->toDateString());
                    break;
            }
        }
    }

    public function dateBetween($model, $col="created_at", $start="date_start", $end="date_end")
    {
        $start = $this->request->input($start, null);
        $end = $this->request->input($end, null);
        if (!is_null($start) && !is_null($end)) {
            $model->where($col, '>=', $start)
                ->where($col, '<=', $end);
        }
    }
}
