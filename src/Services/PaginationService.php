<?php

namespace App\Services;

class PaginationService
{
    private int $page_first_result = 0;
    private int $number_of_page = 1;
    private int $results_per_page;


    public function __construct($objectArray, $results_per_page){
        $this->setResultsPerPage($results_per_page);
        $this->getPagination($objectArray);
    }
    
    public function getPagination($count){
        
        if (!isset ($_GET['page']) ) {
            $page = 1;
        } else {
            $page = $_GET['page'];
        }
        $this->page_first_result = ($page-1) * $this->results_per_page;
        $this->number_of_page = ceil ($count / $this->results_per_page);
        
    }
    
    /**
     * @return int
     */
    public function getPageFirstResult(): int
    {
        return $this->page_first_result;
    }
    
    /**
     * @param int $page_first_result
     */
    public function setPageFirstResult(int $page_first_result): void
    {
        $this->page_first_result = $page_first_result;
    }
    
    /**
     * @param int $results_per_page
     */
    public function setResultsPerPage(int $results_per_page): void
    {
        $this->results_per_page = $results_per_page;
    }
    /**
     * @return int
     */
    public function getNumberOfPage(): int
    {
        return $this->number_of_page;
    }
    
    /**
     * @param int $number_of_page
     */
    public function setNumberOfPage(int $number_of_page): void
    {
        $this->number_of_page = $number_of_page;
    }
    
}
