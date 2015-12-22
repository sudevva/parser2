<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.12.2015
 * Time: 3:48
 */

namespace Parsers\Carid\ContentHandler;


use Parsers\BaseHandler;
use sys\CsvFile;
use sys\Exception\ExpectedParamException;
use sys\Logger\Logger;
use Zend\Dom\Document;
use Zend\Dom\Query;
use Zend\Http\Cookies;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;

class ProductHandler extends BaseHandler
{
    private $content = array();

    public function getFields()
    {
        return array(
            'sku' => array('selector' => 'div.prod_title b[itemprop="sku"]', 'getter' => 'textContent', 'required' => true),
            'product_name' => array('selector' => 'div.prod_title h1', 'getter' => 'textContent'),
            'price' => array('selector' => 'div.prod_price_h span.js-product-price-hide', 'getter' => 'textContent'),
            'img' => array('selector' => 'div.product-images-shifter div.product-images-shifter-source a',
                'getter' => 'getAttribute', 'attr' => 'href'),
            'reviews' => array('callable' => 'parseReview'),
        );
    }

    public function processContent()
    {
        $body = $this->response->getBody();
        $domDocument = new Query($body);
        $this->content = array();
        foreach ($this->getFields() as $fieldName => $field) {
            if (isset($field['selector'])) {
                foreach ($domDocument->execute($field['selector']) as $result) {
                    if (property_exists($result, $field['getter']) && !isset($field['attr'])) {
                        $this->content[$fieldName][] = $result->$field['getter'];
                    }
                    if (method_exists($result, $field['getter']) && isset($field['attr'])) {
                        $this->content[$fieldName][] = $result->{$field['getter']}($field['attr']);
                    }
                }
            }
            if (isset($field['callable']) && method_exists($this, $field['callable'])) {
                $this->$field['callable']($fieldName, $domDocument);
            }
            if (isset($field['required']) && !isset($this->content[$fieldName])) {
                throw new ExpectedParamException('Cant get required property ' . $fieldName . ' with ' . $field['selector'] . ' at ' . $this->url);
            }
            if (isset($this->content[$fieldName])) {
                $this->content[$fieldName] = implode(',', str_replace(',', '[comma]', $this->content[$fieldName]));
            } else {
                $this->content[$fieldName] = '';
            }
        }
        var_dump($this->content);
        $this->content['parse_url'] = $this->url;
        CsvFile::putInFile(array($this->content), $this->fileName);
        Logger::addMessage('Successful! Remove from Queue!');
    }


    private function parseReview($fieldName, Query $domDocument)
    {
        $query = 'div.prod_rvw time'; // Review selector
        foreach ($domDocument->execute($query) as $result) { // Parse review date
            $this->content[$fieldName][] = date('d-m-y',strtotime($result->textContent));
        }
        $result = $domDocument->execute('span.js-prod-rvw-view-more'); // Is more reviews button set

        if ($result->count() > 0) {
            $result = $result->offsetGet(0);
            $pageType = $result->getAttribute('data-page-type');
            $total = $result->getAttribute('data-total');

            if ($total > count($this->content[$fieldName])) { //Can get more reviews
                $cookie = Cookies::fromResponse($this->response, $this->url);
                $this->loader->resetParameters();
                $href = '/submit_review.php';
                if (!parse_url($href, PHP_URL_HOST)) {
                    $urlHost = parse_url($this->url, PHP_URL_HOST);
                    $urlScheme = parse_url($this->url, PHP_URL_SCHEME) . '://';
                    $href = $urlScheme . $urlHost . $href;
                }
                $this->loader->setParameterPost(array(
                    'action' => $this->getReviewAction($pageType),
                    'type' => $result->getAttribute('data-type'),
                    'id' => $result->getAttribute('data-id'),
                    'offset' => count($this->content[$fieldName]),
                ));
                $cookies = new Cookies();
                foreach ($cookie->getAllCookies() as $cookieRaw) { //Zend Cookies Error hack
                    /** @var SetCookie $cookieRaw */
                    if ($cookieRaw->getDomain() != null) {
                        $cookies->addCookie($cookieRaw);
                    }
                }
                $this->loader->addCookie($cookies->getMatchingCookies($this->loader->getUri()));

                $this->loader->setUri($href);
                $request = $this->loader->getRequest();
                $request->getHeaders()->addHeaderLine('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                $request->getHeaders()->addHeaderLine('X-Requested-With', ' XMLHttpRequest');

                $request->setContent($request->getPost()->toString());
                $this->response = $this->loader->setMethod('POST')->send();
                $body = $this->response->getBody();
                $domDocumentNew = new Query($body);
                if ($domDocumentNew->execute($query)->count() == 0) {
                    foreach ($domDocumentNew->execute($query) as $result) { // Parse review date
                        $this->content[$fieldName][] = date('d-m-y',strtotime($result->textContent));
                    }
                    return false;
                } else {
                    $this->parseReview($fieldName, $domDocument);
                }
            }
        }
        return true;
    }

    private function getReviewAction($pageType)
    {
        switch ($pageType) {
            case "manufacturer":
                $action = "getManufacturerReviews";
                break;
            case "ptype_group_traditional":
                $action = "getTraditionalPtypeGroupReviews";
                break;
            case "make_ptype_group_traditional":
            case "make_ptype_group_entrance":
            case "make_ptype":
            case "make_model_ptype_group_traditional":
            case "make_model_ptype_group_entrance":
            case "make_model_ptype":
            case "make_model_year_ptype_group_traditional":
            case "make_model_year_ptype":
            case "make_model_year_ptype_group_entrance":
                $action = "getReviewsByUrlId";
                break;
            case "ptype_group_purpose":
                $action = "getPurposePtypeGroupReviews";
                break;
            case "ptype":
                $action = "getPtypeReviews";
                break;
            case "state":
                $action = "getGeoReviewsState";
                break;
            case "city":
                $action = "getGeoReviewsCity";
                break;
            case "body_type":
                $action = "getReviewsByBodyTypeOffsetLimit";
                break;
            case "all":
                $action = "getReviews";
                break;
            case "super_product":
                $action = "getSuperProductReviews";
                break;
            case "product":
                $action = "getProductReviews";
                break;
            case "mpn_product":
                $action = "getMpnProductReviews";
                break;
            case "get_reviews_by_products":
            case "get_reviews_by_manufacturers":
                $action = $pageType;
        }
        return isset($action) ? $action : null;
    }

} 