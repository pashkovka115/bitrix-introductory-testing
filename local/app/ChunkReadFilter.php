<?php
namespace Ylab;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;


/**
 * Вспомогательный класс для записи HL блоков чанками
 *
 * Class ChunkReadFilter
 * @package YLab
 *
 */
/**  Define a Read Filter class implementing \PhpOffice\PhpSpreadsheet\Reader\IReadFilter  */
class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;
    private $endRow = 0;

    /**  Set the list of rows that we want to read  */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the configured rows
        if (($row >= $this->startRow && $row < $this->endRow)) {
            return true;
        }
        return false;
    }
}