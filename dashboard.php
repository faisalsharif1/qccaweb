<?php

session_start();

if (!isset($_SESSION["loggedin"]) && !$_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="">
    <meta charset="utf-8" />
    <title>Dash Board</title>
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
                                        <div class="col-lg-12 col-xxl-12">

                                            <div class="card card-custom bg-gray-100 card-stretch gutter-b">
                                                <!--begin::Header-->
                                                <div class="card-header border-0 bg-danger py-5">
                                                    <h3
                                                        class="card-title font-weight-bolder text-white dashboard-title marquee">
                                                        قومی کول کمیٹی آخوروال درہ آدم
                                                        خیل
                                                    </h3>
                                                </div>
                                                <!--end::Header-->
                                                <!--begin::Body-->
                                                <div class="card-body p-0 position-relative overflow-hidden">
                                                    <!--begin::Stats-->
                                                    <div class="card-spacer mt-n25">
                                                        <!--begin::Row-->
                                                        <div class="row mt-25 animate__animated animate__fadeInLeft">
                                                            <div
                                                                class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7 ">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24">
                                                                            </rect>
                                                                            <rect fill="#000000" opacity="0.3" x="13"
                                                                                y="4" width="3" height="16" rx="1.5">
                                                                            </rect>
                                                                            <rect fill="#000000" x="8" y="9" width="3"
                                                                                height="11" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="18" y="11" width="3"
                                                                                height="9" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="3" y="13" width="3"
                                                                                height="7" rx="1.5"></rect>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="#"
                                                                    class="text-warning font-weight-bold font-size-h6 dashboard-button-title">
                                                                    ڈیش بورڈ </a>
                                                            </div>
                                                            <div class="col bg-light-warning px-6 py-8 rounded-xl mb-7">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Add-user.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24">
                                                                            </polygon>
                                                                            <path
                                                                                d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                                fill="#000000" fill-rule="nonzero"
                                                                                opacity="0.3"></path>
                                                                            <path
                                                                                d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                                fill="#000000" fill-rule="nonzero">
                                                                            </path>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="companies.php"
                                                                    class="text-primary font-weight-bold font-size-h6 mt-2 dashboard-button-title">کمپنیز</a>
                                                            </div>

                                                        </div>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <!--begin::Row-->
                                                        <div class="row mt-0 animate__animated animate__fadeInRight">
                                                            <div
                                                                class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24">
                                                                            </rect>
                                                                            <rect fill="#000000" opacity="0.3" x="13"
                                                                                y="4" width="3" height="16" rx="1.5">
                                                                            </rect>
                                                                            <rect fill="#000000" x="8" y="9" width="3"
                                                                                height="11" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="18" y="11" width="3"
                                                                                height="9" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="3" y="13" width="3"
                                                                                height="7" rx="1.5"></rect>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="customers.php"
                                                                    class="text-warning font-weight-bold font-size-h6 dashboard-button-title">
                                                                    کسٹمرز </a>
                                                            </div>
                                                            <div class="col bg-light-primary px-6 py-8 rounded-xl mb-7">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Add-user.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <polygon points="0 0 24 0 24 24 0 24">
                                                                            </polygon>
                                                                            <path
                                                                                d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z"
                                                                                fill="#000000" fill-rule="nonzero"
                                                                                opacity="0.3"></path>
                                                                            <path
                                                                                d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z"
                                                                                fill="#000000" fill-rule="nonzero">
                                                                            </path>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="entries.php"
                                                                    class="text-primary font-weight-bold font-size-h6 mt-2 dashboard-button-title">
                                                                    انٹری اپروول </a>
                                                            </div>

                                                        </div>
                                                        <!--end::Row-->
                                                        <!--begin::Row-->
                                                        <div class="row mt-0 animate__animated animate__fadeInUp">
                                                            <div
                                                                class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24">
                                                                            </rect>
                                                                            <rect fill="#000000" opacity="0.3" x="13"
                                                                                y="4" width="3" height="16" rx="1.5">
                                                                            </rect>
                                                                            <rect fill="#000000" x="8" y="9" width="3"
                                                                                height="11" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="18" y="11" width="3"
                                                                                height="9" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="3" y="13" width="3"
                                                                                height="7" rx="1.5"></rect>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="approvedentries.php"
                                                                    class="text-warning font-weight-bold font-size-h6 dashboard-button-title">
                                                                    انٹریز </a>
                                                            </div>

                                                            <div
                                                                class="col bg-light-danger px-6 py-8 rounded-xl mr-7 mb-7">
                                                                <span
                                                                    class="svg-icon svg-icon-3x svg-icon-warning d-block my-2">
                                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Media/Equalizer.svg-->
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                        width="24px" height="24px" viewBox="0 0 24 24"
                                                                        version="1.1">
                                                                        <g stroke="none" stroke-width="1" fill="none"
                                                                            fill-rule="evenodd">
                                                                            <rect x="0" y="0" width="24" height="24">
                                                                            </rect>
                                                                            <rect fill="#000000" opacity="0.3" x="13"
                                                                                y="4" width="3" height="16" rx="1.5">
                                                                            </rect>
                                                                            <rect fill="#000000" x="8" y="9" width="3"
                                                                                height="11" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="18" y="11" width="3"
                                                                                height="9" rx="1.5"></rect>
                                                                            <rect fill="#000000" x="3" y="13" width="3"
                                                                                height="7" rx="1.5"></rect>
                                                                        </g>
                                                                    </svg>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <a href="employeeattendence.php"
                                                                    class="text-warning font-weight-bold font-size-h6 dashboard-button-title">
                                                                    حاضری رپورٹ
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Stats-->
                                                    <div class="resize-triggers">
                                                        <div class="expand-trigger">
                                                            <div style="width: 386px; height: 461px;"></div>
                                                        </div>
                                                        <div class="contract-trigger"></div>
                                                    </div>
                                                </div>
                                                <!--end::Body-->
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
    <!--begin::Global Theme Bundle(used by all pages)-->
    <?php include_once("partials/_footer-javascript.php"); ?>
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>