<?php

namespace App\Services;

abstract class BaseService
{
    protected string $tableActions = "";
    
    public function createTable(array $objectsArray = [])
    {

        if ($objectsArray) {
            $table = '<table class="table table-striped table-sm">
        <thead>
            <tr>
            <th scope="col">#</th>';
            
            $tableHeaders = array_keys($objectsArray[0]);
            foreach ($tableHeaders as $header) {
                if (in_array($header, $this->getExcludedColumns()))
                    continue;
                $header = ucfirst(implode(' ', explode("_", $header)));
                $table .= "<th scope='col'>$header</th>";
            }
            $table .= '<th>Actions</th></th></tr></thead><tbody>';
            foreach ($objectsArray as $key => $attributes) {
                $key += 1;
                $table .= "<tr><th scope='row'>$key</th>";
                foreach ($attributes as $name => $value) {
                    if (in_array($name, $this->getExcludedColumns()))
                        continue;
                    $table .= "<td>$value</td>";
                }
                $table .= $this->generateTableActions($attributes['id']);
            }
            $table .= "</tbody></table>";
            return $table;
        }
    }
    public function createPagination($page_number){
        $pagination ='';
        $query_string = $_SERVER['QUERY_STRING'];
        $query_string = preg_replace('/\??&?page=[0-9]+&?/', '', $query_string);
        if($query_string != ''){
            $query_string = '&' . $query_string;
        }
        if($page_number){
            $pagination = '<nav aria-label="Page navigation example"> <ul class="pagination">';
            for($i = 1; $i <= $page_number; $i++){
                $pagination .= "<li class='page-item'><a class='page-link' href='?page=$i$query_string'>$i</a></li>";
            }
            $pagination .= '</ul></nav>';
        }
        return $pagination;
    }
    abstract public function generateTableActions($id);
    abstract public function getExcludedColumns();
    
}
