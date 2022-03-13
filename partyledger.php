<?php


// Initialize the session
session_start();


if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$FromDate;
$ToDate;

$GLOBALS['TotalCredits'] = 0;
$GLOBALS['TotalDebits'] = 0;



$querytext = "";



function DisplayLedger()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";


    $sql = "select accountid , transactiondate 'transactiondate' , serialno , particulars, amount , rownum , getoldbalance(accountid,?) +  sum(amount) over (order by rownum) 'balance' from (
        select accountid , transactiondate 'transactiondate' , serialno , particulars, amount, row_number() over (order by transactiondate) 'rownum'  from transactions where 
                accountid = ? and cast(transactiondate as date) >= ? and cast(transactiondate as date) < date_add(?,interval 1 day)
            order by transactiondate) as s ";


    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ssss", $FromDate, $AccountId, $FromDate, $ToDate);

        if (!isset($_GET["fromdate"]))
        {
        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        if (!isset($_POST["filterby"])) {
            $AccountId = 0;
        } else {
            $AccountId = $_POST["filterby"];
        }
    }
    else{
        $FromDate = Date('Y-m-d',$_GET["fromdate"]);
        $ToDate = Date('Y-m-d',$_GET["todate"]);
        $AccountId = $_GET["accountid"];
    }
            // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $AccountId, $TransactionDate, $SerialNo, $Particulars, $Amount, $RowNum, $Balance);

            if (mysqli_stmt_num_rows($stmt) > 0) {


                while (mysqli_stmt_fetch($stmt)) :  $GLOBALS['TotalCredits'] += $Amount < 0 ? $Amount : 0;
                $GLOBALS['TotalDebits'] += $Amount > 0 ? $Amount : 0;   ?>

<tr>
    <td class="text-nowrap"> <?php echo date_format(new DateTime($TransactionDate), 'd-m-Y'); ?> </td>
    <td> <?php echo $SerialNo; ?> </td>
    <td style="text-align: right; direction: rtl;"> <?php echo $Particulars; ?> </td>
    <td> <?php echo number_format($Amount) ?> </td>
    <td> <?php echo number_format($Balance);?> </td>
</tr>


<?php
            

                endwhile;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}

function DisplayAccounts()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";

    $sql = "SELECT accountid , accounttitle FROM accounts";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountId, $AccountTitle);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) {
                    if (isset($_POST['filterby']) && $_POST['filterby'] == $AccountId) {
                        echo "<option value= " . $AccountId . " selected>" . $AccountTitle . "</option>";
                    } else {
                        if (isset($_GET['accountid']) && $_GET['accountid'] == $AccountId) {
                            echo "<option value= " . $AccountId . " selected>" . $AccountTitle . "</option>";
                        }
                        else{
                        echo "<option value= " . $AccountId . ">" . $AccountTitle . "</option>";}
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

        // Close connection
        mysqli_close($link);
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <meta charset="utf-8" />
    <title> Entries </title>
    <meta name="description" content="Description here" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
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
                                    <div class="row mb-4">
                                        <div class="col-lg-12">

                                            <div class="card card-primary">

                                                <div class="card-body">
                                                    <form method="post" action="partyledger.php">
                                                        <div class="form-group row">

                                                            <div class="col-lg-3 mt-4">
                                                                <label> From Date :</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_1" name="kt_datepicker_1"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_1"])) {
                                                                                                                                                                                                              if (isset($_GET["fromdate"]))
                                                                                                                                                                                                              {
                                                                                                                                                                                                                echo Date('m/d/Y', html_entity_decode($_GET["fromdate"]));                                                
                                                                                                                                                                                                              }   
                                                                                                                                                                                                              else{                       
                                                                                                                                                                                                                echo date('m/d/Y');
                                                                                                                                                                                                              }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_1"]));
                                                                                                                                                                                                            } ?>">
                                                            </div>
                                                            <div class="col-lg-3 mt-4">
                                                                <label>To Date:</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_2" , name="kt_datepicker_2"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_2"])) {
                                                                                                                                                                                                                 if (isset($_GET["todate"]))
                                                                                                                                                                                                                 {
                                                                                                                                                                                                                   echo Date('m/d/Y', html_entity_decode($_GET["todate"]));                                                
                                                                                                                                                                                                                 }   
                                                                                                                                                                                                                 else{                       
                                                                                                                                                                                                                   echo date('m/d/Y');
                                                                                                                                                                                                                 }
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_2"]));
                                                                                                                                                                                                            } ?>">

                                                            </div>
                                                            <div class="col-lg-5 mt-4">
                                                                <label> Filter By </label>
                                                                <select class="form-control select2" id="filterby"
                                                                    name="filterby">
                                                                    <?php DisplayAccounts() ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-3 mt-12">


                                                                <button type="submit"
                                                                    class="btn btn-success mr-2">Submit</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>



                                        </div>

                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="tbl" class="table table-striped">
                                                            <thead>
                                                                <tr>

                                                                    <th style="width:80px;">تاریخ</th>
                                                                    <th style="width:80px;"> سیریل </th>
                                                                    <th style="width:100%" class="text-right"> تفصیل
                                                                    </th>
                                                                    <th style="width:140px;"> رقم </th>
                                                                    <th style="width:141;"> بیلنس </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php


                                                                DisplayLedger();



                                                                ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3">ٹوٹل </td>                                                                    
                                                                    <td><?php echo number_format($GLOBALS['TotalCredits']); ?></td>
                                                                    <td><?php echo number_format($GLOBALS['TotalDebits']); ?></td>
                                                                </tr>
                                                                                                                                                                                                        </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <?php include_once("partials/_footer-javascript.php"); ?>
    <script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js">


    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js">
    </script>

    <script type="text/javascript">
    $(document).ready(function() {


        $('#filterby').select2({
            width: "100%"
        });

        $('#tbl').DataTable({

            paging: false,
            targets: 'no-sort',
            bSort: false,
            order: []
        });
    });
    </script>
</body>
<!--end::Body-->

</html>