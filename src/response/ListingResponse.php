<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/12/16
 * Time: 10:43 PM
 */

namespace nicolascajelli\server\response;

/**
 * @Doc {"docs": [], "next_page":"","total_count":0}
 */
class ListingResponse extends ApiResponse
{
    /**
     * @var array
     */
    protected $_docs = [];
    protected $_nextPage;
    protected $_totalCount;

    /**
     * ListingResponse constructor.
     */
    public function __construct()
    {
    }

    public function add(\JsonSerializable $doc) : ListingResponse
    {
        $this->_docs[] = $doc;
        return $this;
    }

    public function setTotalCount(int $totalCount) : ListingResponse
    {
        $this->_totalCount = $totalCount;
        return $this;
    }

    protected function getStatus() : string
    {
        return 'ok';
    }

    protected function getData() : array
    {
        return [
            'docs' => $this->_docs,
            'next_page' => $this->_nextPage,
            'total_count' => $this->_totalCount
        ];
    }
}