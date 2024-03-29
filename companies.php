<?php


// Initialize the session
session_start();

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}



function DisplayCompaniesInDashBoard()
{
    require_once "config.php";
    mysqli_set_charset($link, "utf8");
    // Prepare a select statement
    $sql = "SELECT * ,
    companyvehicleouttoday(id) 'totalvehiclesouttoday',
    companyvehicleoutweight(id) 'totalvehiclesouttodayweight',
    companyvehicleoutthismonth(id) 'totalvehiclesoutthismonth' , companyvehicleoutthismonthweight(id) 'totalvehiclesoutthismonthweight' FROM (SELECT ct.customertype , PartyName , sum(Amount) 'Balance' , c.Id 
     FROM companies c Join accounts a on c.accountid = a.accountid
                join transactions t on a.accountid = t.accountid join customertypes ct on ct.id = c.customertypeid WHERE CustomerTypeId = ?
                group by partyname , customertype , c.id) AS S order by totalvehiclesoutthismonth desc";

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_customertypeid);

        // Set parameters
        $param_customertypeid = 1;

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $CustomerType, $Companyname, $Balance, $CompanyId, $TotalVehiclesOutToday, $TotalVehiclesOutTodayWeight, $TotalVehiclesOutThisMonth, $TotalVehiclesOutThisMonthWeight);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>
<div class="col-xl-4">
    <!--begin::Stats Widget 29-->
    <a href="companiesdetail.php?CompanyId=<?php echo $CompanyId; ?>&Date=<?php echo date("Y-m-d") ;?>">
        <div class="card card-custom bgi-no-repeat card-stretch gutter-b"
            style="background-position: right top; background-size: 30% auto; background-image: url(assets/media/svg/shapes/abstract-1.svg)">
            <!--begin::Body-->
            <div class="card-body">


                <div class="row">
                    <div class="col">
                        <span class="svg-icon svg-icon-2x svg-icon-info">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Mail-opened.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <path
                                        d="M6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,12 C19,12.5522847 18.5522847,13 18,13 L6,13 C5.44771525,13 5,12.5522847 5,12 L5,3 C5,2.44771525 5.44771525,2 6,2 Z M7.5,5 C7.22385763,5 7,5.22385763 7,5.5 C7,5.77614237 7.22385763,6 7.5,6 L13.5,6 C13.7761424,6 14,5.77614237 14,5.5 C14,5.22385763 13.7761424,5 13.5,5 L7.5,5 Z M7.5,7 C7.22385763,7 7,7.22385763 7,7.5 C7,7.77614237 7.22385763,8 7.5,8 L10.5,8 C10.7761424,8 11,7.77614237 11,7.5 C11,7.22385763 10.7761424,7 10.5,7 L7.5,7 Z"
                                        fill="#000000" opacity="0.3"></path>
                                    <path
                                        d="M3.79274528,6.57253826 L12,12.5 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 Z"
                                        fill="#000000"></path>
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                        <span class="font-weight-bold font-size-lg menu-title"><?php echo $Companyname; ?></span>
                        <span
                            class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-0 mt-6 d-block"><?php echo number_format($Balance) ?></span>

                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <table class="table table-bordered table-checkable dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr class="thead-light">
                                            <th colspan="2">
                                                <div class="text-center noori-normal-12"> آج کا دن </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="noori-normal ">ٹوٹل گاڑیاں </th>
                                            <th class="noori-normal "> ٹوٹل وزن </th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo number_format($TotalVehiclesOutToday); ?></td>
                                            <td><span
                                                    class="label label-lg font-weight-bold label-light-primary label-inline">
                                                    <?php echo number_format($TotalVehiclesOutTodayWeight) . " " . "ٹن"; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <table class="table table-bordered table-checkable dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr class="thead-light">
                                            <th colspan="2">
                                                <div class="text-center noori-normal-12">مکمل مہینہ</div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="noori-normal "> ٹوٹل گاڑیاں </th>
                                            <th class="noori-normal ">ٹوٹل وزن </th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?php echo number_format($TotalVehiclesOutThisMonth); ?></td>
                                            <td><span
                                                    class="label label-lg font-weight-bold label-light-primary label-inline">
                                                    <?php echo number_format($TotalVehiclesOutThisMonthWeight) . " " . "ٹن"; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>
    </a>
    <!--end::Stats Widget 29-->
</div>

<?php endwhile;
            };
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}

?>


<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <meta charset="utf-8" />
    <title>Companies List</title>
    <meta name="description" content="Description here" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php include_once("partials/_header-stylesheets.php"); ?>
</head>

<!--end::Head-->

<!--begin::Body-->

<body id="kt_body"
    class="page-loading-enabled page-loading header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">

    <!--begin::Page Loader-->
    <?php include("partials/_page-loader.php"); ?>

    <!--begin::Mobile Header-->
    <?php include("partials/_header-mobile.php"); ?>
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="d-flex flex-row flex-column-fluid page">

            <!--begin::sidebar menu-->
            <?php include("partials/_aside.php"); ?>
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

                <!--begin::Header File-->
                <?php include("partials/_header.php"); ?>
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Content File-->
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container-fluid">
                            <!--begin::Row-->

                            <div class="d-flex flex-column-fluid">
                                <!--begin::Container-->
                                <div class="container">
                                    <!--Begin::Row-->
                                    <div class="row">
                                        <?php DisplayCompaniesInDashBoard(); ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!--end::Content-->

                <!--begin::Footer-->
                <?php include("partials/_footer.php"); ?>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->

    <!--begin::Scroll to top-->
    <?php include("partials/_extras/scrolltop.php"); ?>
    <!--begin::Global Theme Bundle(used by all pages)-->
    <?php include_once("partials/_footer-javascript.php"); ?>
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>