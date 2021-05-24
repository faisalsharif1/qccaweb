<?php


// Initialize the session
session_start();

$_TotalExpenses = 0;
$_TotalIncomeCompanies = 0;
$_TotalIncomeOthers = 0;

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$FromDate;
$ToDate;

if (isset($_POST["kt_datepicker_1"])) {
    $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
}

if (isset($_POST["kt_datepicker_2"])) {
    $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
}

$querytext = "";

function DisplayIncome()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

        $sql = "
        select taggedaccount,sum(amount) 'amount' from (
            select case when incexptag = 1 then 'income' else 'expense' end 'type',
                abs(tr.amount) 'amount',
                case when projectidtype = 0 then 'not tagged' when projectidtype = 1 then (select projectname from projects where projects.id = tr.projectid)
                when projectidtype = 2 then (select employeename from employees where employees.id = tr.projectid)
                when projectidtype = 3 then (select accounttitle from accounts where accounts.accountid = tr.projectid) end 'taggedaccount',
                case when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 1) then 'company'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 2) then 'customer'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 3) then 'fixed tekadar'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 4) then 'mate'
                    when projectidtype = 3 and not exists (select * from companies where companies.accountid = tr.projectid) then 'general account' else 'general account'
                end 'accounttype' 
                    from transactions tr join accounts a on a.accountid = tr.accountid
                left join mine m on m.id = tr.mineid                
                where tr.transactiondate >= ? and tr.transactiondate < timestampadd(day,1,?) and  tr.incexptag in (1)) as s where s.accounttype = 'company' group by taggedaccount";
    

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $FromDate, $ToDate);

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountTitle , $TotalIncomeAmount);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo $AccountTitle; ?> </td>
    <td> <?php echo number_format($TotalIncomeAmount,2) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalIncomeCompanies'] += $TotalIncomeAmount;
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


function DisplayIncomeOthers()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

        $sql = "
        select taggedaccount,sum(amount) 'amount' from (
            select case when incexptag = 1 then 'income' else 'expense' end 'type',
                abs(tr.amount) 'amount',
                case when projectidtype = 0 then 'not tagged' when projectidtype = 1 then (select projectname from projects where projects.id = tr.projectid)
                when projectidtype = 2 then (select employeename from employees where employees.id = tr.projectid)
                when projectidtype = 3 then (select accounttitle from accounts where accounts.accountid = tr.projectid) end 'taggedaccount',
                case when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 1) then 'company'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 2) then 'customer'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 3) then 'fixed tekadar'
                    when projectidtype = 3 and exists (select * from companies where companies.accountid = tr.projectid and companies.customertypeid = 4) then 'mate'
                    when projectidtype = 3 and not exists (select * from companies where companies.accountid = tr.projectid) then 'general account' else 'general account'
                end 'accounttype' 
                    from transactions tr join accounts a on a.accountid = tr.accountid
                left join mine m on m.id = tr.mineid                
                where tr.transactiondate >= ? and tr.transactiondate < timestampadd(day,1,?) and  tr.incexptag in (1)) as s where s.accounttype <> 'company' group by taggedaccount";
    

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $FromDate, $ToDate);

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountTitle , $TotalIncomeAmount);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo $AccountTitle; ?> </td>
    <td> <?php echo number_format($TotalIncomeAmount,2) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalIncomeOthers'] += $TotalIncomeAmount;
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



function DisplayExpenses()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

        $sql = "select a.accounttitle , sum(tr.amount) 'totalexpense'
        from transactions tr join accounts a on a.accountid = tr.accountid
    left join mine m on m.id = tr.mineid
where tr.transactiondate >= ? and tr.transactiondate < date_add(?,INTERVAL 1 DAY) and  tr.incexptag in (2)
group by a.accounttitle";
    

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $FromDate, $ToDate);
        

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $AccountTitle , $TotalExpenseAmount);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>
<tr>
    <td> <?php echo $AccountTitle; ?> </td>
    <td> <?php echo number_format($TotalExpenseAmount,2); ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalExpenses'] += $TotalExpenseAmount;
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

?>


<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <meta charset="utf-8" />
    <title> آمدن اور اخراجات  </title>
    <meta name="description" content="Description here" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
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
                                                    <form method="post" action="incomeexpensereport.php">
                                                        <div class="form-group row">
                                                            <div class="col-lg-3 mt-4">
                                                                <label> تاریخ سے لے کر :</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_1" name="kt_datepicker_1"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_1"])) {
                                                                                                                                                                                                                echo date('m/d/Y');
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_1"]));
                                                                                                                                                                                                            } ?>">
                                                            </div>
                                                            <div class="col-lg-3 mt-4">
                                                                <label> تاریخ تک:</label>
                                                                <input type="text" class="form-control"
                                                                    id="kt_datepicker_2" , name="kt_datepicker_2"
                                                                    readonly="readonly" placeholder="Select date"
                                                                    value="<?php if (!isset($_POST["kt_datepicker_2"])) {
                                                                                                                                                                                                                echo date('m/d/Y');
                                                                                                                                                                                                            } else {
                                                                                                                                                                                                                echo Date('m/d/Y', strtotime($_POST["kt_datepicker_2"]));
                                                                                                                                                                                                            } ?>">

                                                            </div>
                                                           
                                                                                                                    
                                                            <div class="col-lg-3 mt-12">
                                                                <button type="submit"
                                                                    class="btn btn-success mr-2"> رپورٹ </button>
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
                                            <table class="table table-bordered">
                                                  <tr>
                                                      <th style="width:50%"></th>
                                                      <th style="width:50%"></th>
                                                  </tr>                                                                                                                                                                  
                                            <tbody>

                                                <tr>
                                                    <td class="pl-20 font-size-h6 bg-light-primary font-weight-bold" style="height:50px;"> کل آمدن</td>
                                                    <td> <h3 id="incometotal1" name="income1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> کل خرچہ </td>
                                                    <td> <h3 id="expense1" name="expense1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> صافی آمدن </td>
                                                    <td> <h3 id="profit1" name="profit1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 bg-light-primary font-weight-bold" style="height:50px;"> کمپنیز آمدن' </td>
                                                    <td> <h3 id="incomecompanies1" name="stock1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> دیگر آمدن </td>
                                                    <td> <h3 id="incomeothers1" name="expesetotal1"></h2> </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>                                   

                                    
                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1> اخراجات  </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl" class="table">
                                                        <thead>
                                                            <tr>

                                                                <th> کھاتہ</th>
                                                                <th> کل رقم </th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                DisplayExpenses();
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td> ٹوٹل اخراجات</td>
                                                                <td><?php echo number_format($GLOBALS['_TotalExpenses']); ?> </td> 
                                                                <input type="hidden" id="expense" value="<?php echo number_format($GLOBALS['_TotalExpenses']); ?>">                                        
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1> آمدن کمپنیز </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl1" class="table">
                                                        <thead>
                                                            <tr>
                                                            <th>کھاتہ</th>                                                            
                                                                <th>رقم</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php

                                                            DisplayIncome();



                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td> ٹوٹل آمدن</td>
                                                                <td><?php echo number_format($GLOBALS['_TotalIncomeCompanies']); ?></td> 
                                                                <input type="hidden" id="incomecompanies" value="<?php echo number_format($GLOBALS['_TotalIncomeCompanies']); ?>">                                        
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1> آمدن دیگر </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl1" class="table">
                                                        <thead>
                                                            <tr>
                                                            <th>کھاتہ</th>                                                            
                                                                <th>رقم</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php

                                                            DisplayIncomeOthers();



                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td> ٹوٹل آمدن </td>
                                                                <td><?php echo number_format($GLOBALS['_TotalIncomeOthers']); ?></td> 
                                                                <input type="hidden" id="incomeothers" value="<?php echo number_format($GLOBALS['_TotalIncomeOthers']); ?>">                                        
                                                                <input type="hidden" id="incometotal" value="<?php echo number_format($GLOBALS['_TotalIncomeOthers'] + $GLOBALS['_TotalIncomeCompanies']); ?>">                                        
                                                                <input type="hidden" id="profit" value="<?php echo number_format(($GLOBALS['_TotalIncomeOthers'] + $GLOBALS['_TotalIncomeCompanies'])-$GLOBALS['_TotalExpenses']); ?>">
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

        setTimeout(() => {
            $('#incomecompanies1').html($('#incomecompanies').val());        
            $('#expense1').html($('#expense').val());
            $('#incomeothers1').html($('#incomeothers').val());
            $('#incometotal1').html($('#incometotal').val());
            $('#profit1').html($('#profit').val());

        }, 1000);

        $('#filterby').select2({
            width: "100%"
        });

        $('#filterby1').select2({
            width: "100%"
        });

        $('#tbl').DataTable({
            paging: false,
            "order": [
                [2, "desc"]
            ]
        });

        $('#tbl2').DataTable();
    });
    </script>
</body>
<!--end::Body-->

</html>