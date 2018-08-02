<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 05/07/2018
 * Time: 11:17
 */

namespace App\Service;


use App\Entity\Pojo\PaginatedResults;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    const DEFAULT_RESULTS_PER_PAGE = 20;

    public function __construct()
    {
    }

    public function generatePaginatedResults(Request $request, array $results): PaginatedResults {
        $resultsPerPage = $request->get('resultsPerPage');
        $currentPage = $request->get('p');

        $resultsPerPage = strlen($resultsPerPage) > 0 ? intval($resultsPerPage) : null;
        $currentPage = strlen($currentPage) > 0 ? intval($currentPage) : null;

        if ($resultsPerPage === null && $currentPage === null
            || ($resultsPerPage <= 0 || $currentPage <= 0)) {
            $totalPages = null;
            $currentPage = null;
            $resultsPerPage = null;
            $pageResults = $results;
        } else {
            $resultsPerPage = $resultsPerPage ?? self::DEFAULT_RESULTS_PER_PAGE;
            $totalPages = ceil(count($results) / $resultsPerPage);

            if ($currentPage > $totalPages) {
                $currentPage = 1;
            }
            $start = ($currentPage - 1) * $resultsPerPage;
            $pageResults = array_slice($results, $start, $resultsPerPage);
        }

        $ret = new PaginatedResults();
        $ret->setResults($pageResults)
            ->setResultsPerPage($resultsPerPage)
            ->setTotalPages($totalPages)
            ->setTotalResults(count($results))
            ->setCurrentPage($currentPage);
        return $ret;
    }
}