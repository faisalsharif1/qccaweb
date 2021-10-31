<?php


// Initialize the session
session_start();

$_TotalTons = 0;
$_TotalValue = 0;
$_TotalExpenses = 0;
$_TotalAssignedQuantity = 0;
$_TotalAssignedValue=0;

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

function DisplayAttendences()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

    if (isset($_POST["filterby1"]) ) {
        $sql = "SELECT kg.entrydatetime 'EntryDate' , ckl.vehiclenumber , c.partyname , ckl.weightintons - kg.weightintons 'TotalTons'  , 
        ckl.totalprice	FROM kanta_general kg JOIN company_kanta_log ckl ON kg.id = ckl.kanta_general_id
           JOIN companies c ON c.id = kg.companyid
           JOIN mine m ON m.id = ckl.mineid
       WHERE cast(kg.entrydatetime as date) >= ? and cast(kg.entrydatetime as date) < date_add(?,interval 1 day) and ckl.MineId = ?";
    } 

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        if (isset($_POST["filterby1"])) {
            mysqli_stmt_bind_param($stmt, "sss", $FromDate, $ToDate,$MineId);
        }

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        
        if (!isset($_POST["filterby1"])) {
            $MineId = 0;
        } else {
            $MineId = $_POST["filterby1"];
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $EntryDate, $VehicleNumber, $PartyName, $TotalTons, $TotalPrice);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo date_format(new DateTime($EntryDate), 'd-m-Y'); ?> </td>
    <td> <?php echo $VehicleNumber; ?> </td>
    <td class="noori-normal-12"> <?php echo $PartyName; ?> </td>
    <td><?php echo number_format($TotalPrice/$TotalTons,2); ?></td>
    <td> <?php echo number_format($TotalTons,2) ?> </td>
    <td> <?php echo number_format($TotalPrice) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalTons'] += $TotalTons;
                    $GLOBALS['_TotalValue'] += $TotalPrice;
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


function DisplayStock()
{
    require "config.php";


    mysqli_set_charset($link, "utf8");
    $sql = "";

    if (isset($_POST["filterby1"]) ) {
        $sql = "select sa.assigneddateTime,m.minedescription , i.itemname , sa.quantity , sa.totalprice FROM items i join
        stockassignments sa ON i.id = sa.itemId
    JOIN mine m ON m.id = sa.mineid
WHERE cast(sa.assigneddatetime as date) >= ? and cast(sa.assigneddatetime as date)  < date_add(?,interval 1 day) AND sa.mineid = ?";
    } 

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        if (isset($_POST["filterby1"])) {
            mysqli_stmt_bind_param($stmt, "sss", $FromDate, $ToDate,$MineId);
        }

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        
        if (!isset($_POST["filterby1"])) {
            $MineId = 0;
        } else {
            $MineId = $_POST["filterby1"];
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $EntryDate, $MineDescription, $ItemName, $Quantity, $TotalPrice);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>


<tr>
    <td> <?php echo date_format(new DateTime($EntryDate), 'd-m-Y'); ?> </td>
    <td> <?php echo $ItemName; ?> </td>
    <td> <?php echo number_format($Quantity) ?> </td>
    <td> <?php echo number_format($TotalPrice) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalAssignedQuantity'] += $Quantity;
                    $GLOBALS['_TotalAssignedValue'] += $TotalPrice;
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

    if (isset($_POST["filterby1"]) ) {
        $sql = "SELECT a.accounttitle , t.transactiondate , t.particulars , t.amount , m.minedescription  , t.SerialNo
        FROM accounts a JOIN transactions t ON a.accountid = t.AccountId
            JOIN mine m ON m.id = t.mineid
        WHERE cast(t.transactiondate as date) >= ? AND cast(t.transactiondate as date) < date_add(?,interval 1 day) 
            and t.mineid = ? AND t.amount > 0";
    } 

    if ($stmt = mysqli_prepare($link, $sql)) {
        // Bind variables to the prepared statement as parameters
        if (isset($_POST["filterby1"])) {
            mysqli_stmt_bind_param($stmt, "sss", $FromDate, $ToDate,$MineId);
        }

        if (!isset($_POST["kt_datepicker_1"])) {
            $FromDate = Date('Y-m-d');
            $ToDate = Date('Y-m-d');
        } else {
            $FromDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_1"]));
            $ToDate = Date('Y-m-d', strtotime($_POST["kt_datepicker_2"]));
        }

        
        if (!isset($_POST["filterby1"])) {
            $MineId = 0;
        } else {
            $MineId = $_POST["filterby1"];
        }

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt, $AccountTitle , $EntryDate, $Particulars, $Amount, $MineDescription, $Serial);

            if (mysqli_stmt_num_rows($stmt) > 0) {



                while (mysqli_stmt_fetch($stmt)) : ?>
<tr class="bg-light-primary" style="height:20px;">
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
    <td class="noori-normal-12" colspan="5"> <?php echo $Particulars; ?> </td>
</tr>
<tr>
    <td> <?php echo $AccountTitle; ?> </td>
    <td> <?php echo date_format(new DateTime($EntryDate), 'd-m-Y'); ?> </td>
    <td> <?php echo number_format($Amount,2) ?> </td>
    <td class="noori-normal-12"> <?php echo $MineDescription; ?> </td>
    <td> <?php echo number_format($Serial) ?> </td>
</tr>

<?php 
                    $GLOBALS['_TotalExpenses'] += $Amount;
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


function DisplayMines()
{

    require "config.php";

    mysqli_set_charset($link, "utf8");
    $sql = "";

    $sql = "SELECT id,minedescription FROM mine where minesownedby = 2";

    if ($stmt = mysqli_prepare($link, $sql)) {

        // Attempt to execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            // Store result
            mysqli_stmt_store_result($stmt);

            mysqli_stmt_bind_result($stmt,  $AccountId, $AccountTitle);

            if (mysqli_stmt_num_rows($stmt) > 0) {

                while (mysqli_stmt_fetch($stmt)) {
                    if (isset($_POST['filterby1']) && $_POST['filterby1'] == $AccountId) {
                        echo "<option value= " . $AccountId . " selected>" . $AccountTitle . "</option>";
                    } else {
                        echo "<option value= " . $AccountId . ">" . $AccountTitle . "</option>";
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

function displayclasses1()
{
        
    if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "1"))
    {
        echo "row mb-4";
    }
    else
    {
        echo "row mb-4 hidden";
    }
}

function displayclasses2()
{
        
    if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "2"))
    {
        echo "row mb-4";
    }
    else
    {
        echo "row mb-4 hidden";
    }
}

function displayclasses3()
{
        
    if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "3"))
    {
        echo "row mb-4";
    }
    else
    {
        echo "row mb-4 hidden";
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
                                                    <form method="post" action="minedetailed.php">
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
                                                           
                                                            <div class="col-lg-5 mt-4">
                                                                <label> مائن </label>
                                                                <select class="form-control select2" id="filterby1"
                                                                    name="filterby1">
                                                                   
                                                                    <?php DisplayMines() ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-5 mt-4">
                                                                <label> ڈیٹا کی قسم </label>
                                                                <select class="form-control" id="filterdata"
                                                                    name="filterdata">
                                                                    <option value="0" selected>سب دکھائیں</option> 
                                                                    <option value="1" >گاڑیوں کی ترسیل</option> 
                                                                    <option value="2" >اخراجات</option> 
                                                                    <option value="3" > مائن اشیاء </option> 
                                                                </select>
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
                                                    <td> <h3 id="income1" name="income1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> خرچہ </td>
                                                    <td> <h3 id="expense1" name="expense1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 bg-light-primary font-weight-bold" style="height:50px;"> سٹاک خرچہ </td>
                                                    <td> <h3 id="stock1" name="stock1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> ٹوٹل خرچہ </td>
                                                    <td> <h3 id="expensetotal1" name="expesetotal1"></h2> </td>
                                                </tr>
                                                <tr>
                                                    <td class="pl-20 font-size-h6 font-weight-bold" style="height:50px;"> منافع </td>
                                                    <td> <h3 id="profit1" name="profit1"></h2> </td>
                                                </tr>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>                                   

                                    
                                    <div class="<?php displayclasses1(); ?>">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1>گاڑیوں کی ترسیل </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl" class="table">
                                                        <thead>
                                                            <tr>

                                                                <th>تاریخ</th>
                                                                <th>گاڑی نمبر</th>
                                                                <th>پارٹی ںام</th>
                                                                <th>فی ٹن</th>
                                                                <th>ٹوٹل ٹن</th>
                                                                <th>ٹوٹل قیمت</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php

if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "1"))
{
    DisplayAttendences();
}
                                                            



                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td><?php echo number_format($GLOBALS['_TotalTons']); ?>
                                                                </td>
                                                                <td><input type="hidden" id="income" name="income" value="<?php echo number_format($GLOBALS['_TotalValue']); ?>"><?php echo number_format($GLOBALS['_TotalValue']); ?>
                                                                </td>                                                             
                                                                
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="<?php displayclasses2(); ?>">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1> اخراجات </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl1" class="table">
                                                        <thead>
                                                            <tr>
                                                            <th>کھاتہ</th>
                                                                <th>تاریخ</th>                                                              
                                                                <th>رقم</th>
                                                                <th>مائن</th>
                                                                <th>سیریل</th>                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php

if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "2"))
{
                                                            DisplayExpenses();
}


                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td></td>
                                                                <td></td>
                                                                <td><input type="hidden" id="expense" name="expense" value="<?php echo number_format($GLOBALS['_TotalExpenses']); ?>"><?php echo number_format($GLOBALS['_TotalExpenses']); ?></td>                                                               
                                                                <td></td>
                                                                <td></td>   
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="<?php displayclasses3(); ?>">
                                        <div class="col-lg-12">
                                            <div class="card card-primary">
                                            <div class="card-header">
                                            <h1> مائن اشیاء </h1>
                                            </div>
                                                <div class="card-body">

                                                    <table id="tbl2" class="table">
                                                        <thead>
                                                            <tr>
                                                            
                                                                <th>تاریخ</th>                                                              
                                                                <th>نام اشیاء</th>
                                                                <th>مقدار</th>
                                                                <th>کل قیمت</th>                                                               
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php

if (isset($_POST["filterdata"]) && ($_POST["filterdata"] == "0" || $_POST["filterdata"] == "3"))
{
                                                                DisplayStock();
}


                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td><input type="hidden" id="expensestotal" name="expensestotal" value="<?php echo number_format($GLOBALS['_TotalAssignedValue'] + $GLOBALS['_TotalExpenses']); ?>"></td>
                                                                <td><input type="hidden" id="profit" name="profit" value="<?php echo number_format($GLOBALS['_TotalValue']-($GLOBALS['_TotalAssignedValue'] + $GLOBALS['_TotalExpenses'])); ?>"></td>
                                                                <td><?php echo number_format($GLOBALS['_TotalAssignedQuantity']); ?></td>                                                               
                                                                <td><input type="hidden" id="stock" name="stock" value="<?php echo number_format($GLOBALS['_TotalAssignedValue']); ?>">
                                                                                    <?php echo number_format($GLOBALS['_TotalAssignedValue']); ?></td>
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
            $('#income1').html($('#income').val());        
            $('#expense1').html($('#expense').val());
            $('#stock1').html($('#stock').val());
            $('#profit1').html($('#profit').val());

            $('#expensetotal1').html($('#expensestotal').val());
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