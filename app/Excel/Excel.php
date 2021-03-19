<?php


namespace App\Excel;

use App\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Http\Message\ResponseInterface;


class Excel implements Importer, Exporter
{
    use Verify;
    /**
     * @var array
     */
    private $closures = [];
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    public function setClosure($name, \Closure $closure): void
    {
        $this->closures[$name] = $closure;
    }

    public function spreadsheet($filePath=null): Spreadsheet
    {
        if (is_null($filePath)) {
            return $this->spreadsheet = new Spreadsheet();
        }

        $this->spreadsheet = IOFactory::load($filePath);
        return $this->spreadsheet;
    }

    public function rowFormat($formats): bool
    {
        try {

            // 设置单元格宽度
            if (isset($formats['widths'])) {
                foreach ($formats['widths'] as $k => $v) {
                    $this->spreadsheet->getActiveSheet()->getColumnDimension($k)->setWidth($v);
                }
            }

            // 设置单元格合并
            if (isset($formats['mergeCells'])) {
                foreach ($formats['mergeCells'] as $k => $v) {
                    $this->spreadsheet->getActiveSheet()->mergeCells($v);
                }
            }

            // 设置样式
            if (isset($formats['styles'])) {
                foreach ($formats['styles'] as $k => $v) {
                    $this->spreadsheet->getActiveSheet()->getStyle($k)->applyFromArray($v);
                }
            }

            // 设置单元格值
            if (isset($formats['cellValue'])) {
                foreach ($formats['cellValue'] as $k => $v) {
                    $this->spreadsheet->getActiveSheet()->setCellValue($k, $v);
                }
            }

        } catch (\Exception $e) {
            log_standard_error($e);
            return false;
        }
        return true;
    }

    public function verifyRow(int $line, array $header, array $cols)
    {
        $errors = [];
        if (empty($this->rules)) {
            return true;
        }

        foreach ($this->rules as $k => $v) {
            $func = explode(',', $v['func']);
            $error = false;
            foreach ($func as $kk => $vv) {
                switch ($vv) {
                    case 'checkNotEmpty':
                        $error = $this->checkNotEmpty($cols[$k]);
                        break;
                    case 'checkMobile':
                        $error = $this->checkMobile($cols[$k]);
                        break;
                    case 'checkMoreThan':
                        $error = $this->checkMoreThan($cols[$k], $v['args']);
                        break;
                    case 'checkKeyExists':
                        $error = $this->checkKeyExists($cols[$k], $v['args']);
                        break;
                    case 'checkNumber':
                        $error = $this->checkNumber($cols[$k], $v['args']);
                        break;
                    default:
                        $error = true;
                }
            }

            if (is_string($error)) {
                $errors[] = "表格{$line}行-{$k}列($header[$line][$k])，{$error}";
            }
        }

        return $errors;

    }

    public function response($ext='xlsx')
    {
        try {
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, ucfirst($ext));

            $filePath = tmp_path(get_unique_id().'.'.$ext);

            $writer->save($filePath);

        } catch (Exception $e) {
            log_standard_error($e);
            return false;
        }
        return $filePath;
    }

    /**
     * 导出
     * @param $export
     * @param string $name
     * @return ResponseInterface|array
     */
    public function download($export, string $name)
    {
        try {
            $this->spreadsheet = $this->spreadsheet();

            $this->spreadsheet;
            $data = $export->collection();

            // 设置行格式
            if (method_exists($export, 'format')) {
                $this->rowFormat($export->format());
            }

            $workSheet = $this->spreadsheet->getActiveSheet();
            $startRow = 1;

            if (method_exists($export, 'startRow')) {
                $startRow = $export->startRow();
            }

            $rows = [];

            if (method_exists($export, 'headings')) {
                $rows[] = $export->headings();
            }

            $rows = array_merge($rows, $data);


            $workSheet->fromArray(
                $rows,
                null,
                'A'.$startRow
            );

            $filePath = $this->response();

            if (!$filePath) {
                return resp(400, '上传失败');
            }

            return response()->download($filePath, $name);

        } catch (\Exception $e) {
            log_standard_error($e);
            throw new \RuntimeException($e);
        }

    }

    /**
     * 导入
     * @param object $import
     * @param string $path
     * @return array
     */
    public function import(object $import, string $path): array
    {
        try {
            $this->spreadsheet = $this->spreadsheet($path);

            $rows = $this->spreadsheet->getActiveSheet()->toArray();

            $startRow = 1;

            if (method_exists($import, 'startRow')) {
                $startRow = $import->startRow();
            }

            // 注入读取到的数据
            $res = $import->collection(collect($rows), $startRow);

            if ($res['code'] !== 200) {
                return $res;
            }

        } catch (\Exception $e) {
            log_standard_error($e);
            return resp(400, '执行 Excel 失败');
        }
        return resp(200, 'ok');
    }
}
