<?php

namespace Norse\IPViking;

class Settings_GeoFilter extends Request {
    protected $_collection;

    public function __construct($config) {
        parent::__construct($config);
    }

    public function setCollection(Settings_GeoFilter_Collection $collection) {
        $this->_collection = $collection;
    }

    public function getCollection() {
        return $this->_collection;
    }

    protected function _getGeoFilterXML() {
        $collection = $this->getCollection();

        if (empty($collection)) return null;
        return $this->getCollection()->getGeoFilterXML();
    }

    protected function _getBodyFields() {
        $body_fields = parent::_getBodyFields();

        $body_fields['method']       = 'geofilter';
        $body_fields['geofilterxml'] = $this->_getGeoFilterXML();

        return $body_fields;
    }

    protected function _getCurlOpts() {
        $curl_opts = parent::_getCurlOpts();

        $curl_opts[CURLOPT_POST]       = true;
        $curl_opts[CURLOPT_POSTFIELDS] = $this->_getEncodedBody();
        $curl_opts[CURLOPT_HTTPHEADER] = $this->_getHttpHeader();

        return $curl_opts;
    }

    public function process() {
        $this->_setCurlOpts();

        $curl_response = parent::_curlExec();
        $curl_info     = parent::_curlInfo();

        return new Settings_GeoFilter_Collection($curl_response, $curl_info);
    }

    public function getCurrentSettings() {
        return $this->process();
    }

    public function addGeoFilter(Settings_GeoFilter_Filter $filter) {
        $filter->setCommand('add');
        $this->setCollection(new Settings_GeoFilter_Collection(array($filter)));

        return $this->process();
    }

    public function deleteGeoFilter(Settings_GeoFilter_Filter $filter) {
        $filter->setCommand('delete');
        $this->setCollection(new Settings_GeoFilter_Collection(array($filter)));

        return $this->process();
    }

    public function updateGeoFilters(array $geofilters) {
        foreach ($geofilters as &$filter) {
            if (!$filter instanceof Settings_GeoFilter_Filter) {
                $filter = new Settings_GeoFilter_Filter($filter);
            }
        }

        $this->setCollection(new Settings_GeoFilter_Collection($geofilters));

        return $this->process();
    }

}
