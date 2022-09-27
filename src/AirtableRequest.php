<?php

namespace Zlt\Airtable;

class AirtableRequest
{
    public ?string $filterByFormula;
    public ?int $maxRecords;
    public ?int $pageSize;
    public ?array $sort;
    public ?string $view;
    public ?string $offset;
    public ?string $cellFormat;
    public ?string $timeZone;
    public ?array $fields;
    public ?string $returnFieldsByFieldId;
    public ?string $userLocale;

    public function __construct(?string $filterByFormula = null,
                                ?int    $maxRecords = null,
                                ?int    $pageSize = null,
                                ?array  $sort = null,
                                ?string $view = null,
                                ?string $offset = null,
                                ?string $cellFormat = null,
                                ?string $timeZone = null,
                                ?array  $fields = null,
                                ?string $returnFieldsByFieldId = null,
                                ?string $userLocale = null)
    {
        $this->filterByFormula = $filterByFormula;
        $this->maxRecords = $maxRecords;
        $this->pageSize = $pageSize;
        $this->sort = $sort;
        $this->view = $view;
        $this->offset = $offset;
        $this->cellFormat = $cellFormat;
        $this->timeZone = $timeZone;
        $this->fields = $fields;
        $this->returnFieldsByFieldId = $returnFieldsByFieldId;
        $this->userLocale = $userLocale;
    }

    public function getParametersQuery(): string
    {
        $query = '';
        if (!empty($this->fields)) {
            foreach ($this->fields as $field) {
                if ($query != '') {
                    $query .= '&';
                }
                $query .= 'fields%5B%5D=' . $field;
            }
        }
        if ($this->filterByFormula) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'filterByFormula=' . urlencode($this->filterByFormula);
        }

        if(!empty($this->sort)){
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'sort=' . $this->getSortValues();
        }

        if($this->cellFormat){
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'cellFormat=' . $this->cellFormat;
        }

        if($this->timeZone){
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'timeZone=' . $this->timeZone;
        }

        if ($this->offset) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'offset=' . $this->offset;
        }

        if ($this->view) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'view=' . urlencode($this->view);
        }

        if ($this->maxRecords) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'maxRecords=' . $this->maxRecords;
        }

        if ($this->pageSize) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'pageSize=' . $this->pageSize;
        }

        if ($this->returnFieldsByFieldId) {
            if ($query != '') {
                $query .= '&';
            }
            $query .= 'returnFieldsByFieldId=true';
        }
        return $query;
    }

    private function getSortValues(){
        $query = '';
        foreach($this->sort as $key=>$sort){
            foreach(array_keys($sort) as $attribute){
                if ($query != '') {
                    $query .= '&';
                }
                $query .= urlencode("sort[$key][$attribute]={$sort[$attribute]}");
            }
        }
    }
}
