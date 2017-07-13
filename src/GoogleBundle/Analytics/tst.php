<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 04/07/17
 * Time: 3:03 PM
 */

namespace GoogleBundle\Analytics;



use Doctrine\Common\Collections\ArrayCollection;

class tst
{
    private $KEY_FILE_LOCATION;
    private $analytics;
    private $reports;


    public function __construct()
    {
        $this->KEY_FILE_LOCATION = __DIR__ . '/service-account-credentials.json';
        $client = new \Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($this->KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->analytics = new \Google_Service_AnalyticsReporting($client);
    }



    /**
     * Queries the Analytics Reporting API V4.
     *
     * @param \Google_Service_AnalyticsReporting service An authorized Analytics Reporting API V4 service object.
     * @return \Countable The Analytics Reporting API V4 response.
     */
    public function getReport() {

        // Replace with your view ID, for example XXXX.
        $VIEW_ID = "134724194";

        // Create the DateRange object.
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("7daysAgo");
        $dateRange->setEndDate("today");

        // Create the Metrics object.
        $sessions = new \Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:totalEvents");
        $sessions->setAlias("totalEvents");

        $something = new \Google_Service_AnalyticsReporting_Dimension();
        $something->setName("ga:eventCategory");

        $something2 = new \Google_Service_AnalyticsReporting_Dimension();
        $something2->setName("ga:eventLabel");


        //$filter = new \Google_Service_AnalyticsReporting_DimensionFilterClause();
        //$filter->setFilters();


        // Create the ReportRequest object.
        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($sessions));
        $request->setDimensions(array($something, $something2));

        //dump($request);die();
        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        return $this->analytics->reports->batchGet( $body );
    }


    /**
     * Parses and prints the Analytics Reporting API V4 response.
     *
     * @param \Google_Service_Resource An Analytics Reporting API V4 response.
     */
    public function printResults($reports) {

        $output = "";

        for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
            /** @var \Google_Service_AnalyticsReporting_Report $report */
            $report = $reports[ $reportIndex ];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                /** @var \Google_Service_AnalyticsReporting_ReportRow $row */
                $row = $rows[ $rowIndex ];
                $dimensions = $row->getDimensions();
                /** @var ArrayCollection $metrics */
                $metrics = $row->getMetrics();
                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    $output .= $dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n";
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        /** @var \Google_Service_AnalyticsReporting_MetricHeaderEntry $entry */
                        $entry = $metricHeaders[$k];
                        $output .= $entry->getName() . ": " . $values[$k] . "\n";
                    }
                }
            }
        }

        return $output;
    }

}