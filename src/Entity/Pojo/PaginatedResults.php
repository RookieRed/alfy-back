<?php
/**
 * Created by PhpStorm.
 * User: celoundou-ngah
 * Date: 05/07/2018
 * Time: 11:18
 */

namespace App\Entity\Pojo;


use Symfony\Component\Serializer\Annotation\Groups;

class PaginatedResults
{
    /**
     * @var int|null
     * @Groups({"pagination"})
     */
    private $resultsPerPage;
    /**
     * @var int|null
     * @Groups({"pagination"})
     */
    private $currentPage;
    /**
     * @var int|null
     * @Groups({"pagination"})
     */
    private $totalPages;
    /**
     * @var int|null
     * @Groups({"pagination"})
     */
    private $totalResults;
    /**
     * @var array
     * @Groups({"pagination"})
     */
    private $results;

    public function __construct()
    {
        $this->results = [];
    }

    /**
     * @return int
     */
    public function getResultsPerPage(): ?int
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     * @return PaginatedResults
     */
    public function setResultsPerPage(?int $resultsPerPage): self
    {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage(): ?int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return PaginatedResults
     */
    public function setCurrentPage(?int $currentPage): self
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): ?int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return PaginatedResults
     */
    public function setTotalPages(?int $totalPages): self
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param array $results
     * @return PaginatedResults
     */
    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTotalResults(): ?int
    {
        return $this->totalResults;
    }

    /**
     * @param int|null $totalResults
     * @return PaginatedResults
     */
    public function setTotalResults(?int $totalResults): self
    {
        $this->totalResults = $totalResults;
        return $this;
    }

}