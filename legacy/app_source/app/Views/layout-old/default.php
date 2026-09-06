<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url(); ?>/assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="<?= base_url(); ?>/assets/img/icon-title.png" />
    <title> <?= $title; ?> </title>

    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet" />

    <!-- Nucleo Icons -->
    <link href="<?= base_url(); ?>/assets/css/nucleo-icons.css" rel="stylesheet" />

    <!-- CSS Files -->
    <link href="<?= base_url(); ?>/assets/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
    <link href="<?= base_url(); ?>/assets/css/custom.css" rel="stylesheet" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="<?= base_url(); ?>/assets/demo/demo.css" rel="stylesheet" />

    <!-- add from stisla -->
    <!-- <link rel="stylesheet" href="<?= base_url() ?>/assets/vendor/bootstrap/dist/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" /> -->

</head>

<body class="dark-theme dark-content sidebar-mini" id="change-sidebar">
    <div class="wrapper">

        <!-- sidebar -->
        <?= $this->include('layout/sidebar'); ?>

        <div class="main-panel">

            <!-- Navbar -->
            <?= $this->include('layout/navbar'); ?>
            <!-- End Navbar -->
            <div class="content">
                <!-- main content -->
                <?= $this->renderSection('content') ?>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="copyright">
                        ©
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        made with <i class="tim-icons icon-heart-2"></i> by <a href="javascript:void(0)" target="_blank">Mang John</a> for a better web.
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <div class="fixed-plugin">
        <div class="dropdown show-dropdown">
            <a href="#" data-toggle="dropdown">
                <i class="fa fa-cog fa-2x"> </i>
            </a>
            <ul class="dropdown-menu">
                <li class="header-title">Sidebar Background</li>
                <li class="adjustments-line">
                    <a href="javascript:void(0)" class="switch-trigger background-color">
                        <div class="badge-colors text-center">
                            <span class="badge filter badge-primary active" data-color="primary"></span>
                            <span class="badge filter badge-info" data-color="blue"></span>
                            <span class="badge filter badge-success" data-color="green"></span>
                        </div>
                        <div class="clearfix"></div>
                    </a>
                </li>
                <li class="header-title">
                    Sidebar Mini
                </li>
                <li class="adjustments-line">
                    <div class="togglebutton switch-sidebar-mini">
                        <span class="label-switch">OFF</span>
                        <input type="checkbox" name="checkbox" checked class="bootstrap-switch" data-on-label="" data-off-label="" />
                        <span class="label-switch label-right">ON</span>
                    </div>
                    <div class="togglebutton switch-change-color mt-3">
                        <span class="label-switch">LIGHT MODE</span>
                        <input type="checkbox" name="checkbox" checked="" class="bootstrap-switch" data-on-label="" data-off-label="">
                        <span class="label-switch label-right">DARK MODE</span>
                    </div>
                </li>
                <!-- <li class="adjustments-line text-center color-change">
                    <span class="color-label">LIGHT MODE</span>
                    <span class="badge light-badge mr-2"></span>
                    <span class="badge dark-badge ml-2"></span>
                    <span class="color-label">DARK MODE</span>
                </li> -->
                <li class="button-container mt-lg-5">
                    <a href="https://www.creative-tim.com/product/black-dashboard" target="_blank" class="btn btn-primary btn-block btn-round">Download Now</a>
                    <a href="https://demos.creative-tim.com/black-dashboard/docs/1.0/getting-started/introduction.html" target="_blank" class="btn btn-default btn-block btn-round"> Documentation </a>
                </li>
                <li class="header-title">Thank you for 95 shares!</li>
                <li class="button-container text-center">
                    <button id="twitter" class="btn btn-round btn-info"><i class="fab fa-twitter"></i> &middot; 45</button>
                    <button id="facebook" class="btn btn-round btn-info"><i class="fab fa-facebook-f"></i> &middot; 50</button>
                    <br />
                    <br />
                    <a class="github-button" href="https://github.com/creativetimofficial/black-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star ntkme/github-buttons on GitHub">Star</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?= base_url('logout'); ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="<?= base_url(); ?>/assets/js/core/jquery.min.js"></script>
    <script src="<?= base_url(); ?>/assets/js/core/popper.min.js"></script>
    <script src="<?= base_url(); ?>/assets/js/core/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>/assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?= base_url(); ?>/assets/js/plugins/bootstrap-switch.js"></script>
    <script src="<?= base_url(); ?>/assets/js/plugins/bootstrap-tagsinput.js"></script>
    <!--  Google Maps Plugin    -->
    <!-- Place this tag in your head or just before your close body tag. -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
    <!-- Chart JS -->
    <script src="<?= base_url(); ?>/assets/js/plugins/chartjs.min.js"></script>
    <!--  Notifications Plugin    -->
    <script src="<?= base_url(); ?>/assets/js/plugins/bootstrap-notify.js"></script>
    <!-- Control Center for Black Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= base_url(); ?>/assets/js/black-dashboard.min.js?v=1.0.0"></script>
    <!-- Black Dashboard DEMO methods, don't include it in your project! -->
    <script src="<?= base_url(); ?>/assets/demo/demo.js"></script>
    <script>
        $(document).ready(function() {
            $().ready(function() {
                $sidebar = $(".sidebar");
                $navbar = $(".navbar");
                $main_panel = $(".main-panel");

                $full_page = $(".full-page");

                $sidebar_responsive = $("body > .navbar-collapse");
                sidebar_mini_active = true;
                white_color = false;

                window_width = $(window).width();

                fixed_plugin_open = $(".sidebar .sidebar-wrapper .nav li.active a p").html();

                $(".fixed-plugin a").click(function(event) {
                    if ($(this).hasClass("switch-trigger")) {
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        } else if (window.event) {
                            window.event.cancelBubble = true;
                        }
                    }
                });

                $(".fixed-plugin .background-color span").click(function() {
                    $(this).siblings().removeClass("active");
                    $(this).addClass("active");

                    var new_color = $(this).data("color");

                    if ($sidebar.length != 0) {
                        $sidebar.attr("data", new_color);
                    }

                    if ($main_panel.length != 0) {
                        $main_panel.attr("data", new_color);
                    }

                    if ($full_page.length != 0) {
                        $full_page.attr("filter-color", new_color);
                    }

                    if ($sidebar_responsive.length != 0) {
                        $sidebar_responsive.attr("data", new_color);
                    }
                });

                $(".switch-sidebar-mini input").on("switchChange.bootstrapSwitch", function() {
                    var $btn = $(this);

                    if (sidebar_mini_active == true) {
                        $("body").removeClass("sidebar-mini");
                        sidebar_mini_active = false;
                        blackDashboard.showSidebarMessage("Sidebar mini deactivated...");
                    } else {
                        $("body").addClass("sidebar-mini");
                        sidebar_mini_active = true;
                        blackDashboard.showSidebarMessage("Sidebar mini activated...");
                    }

                    // we simulate the window Resize so the charts will get updated in realtime.
                    var simulateWindowResize = setInterval(function() {
                        window.dispatchEvent(new Event("resize"));
                    }, 180);

                    // we stop the simulation of Window Resize after the animations are completed
                    setTimeout(function() {
                        clearInterval(simulateWindowResize);
                    }, 1000);
                });

                $(".switch-change-color input").on("switchChange.bootstrapSwitch", function() {
                    var $btn = $(this);

                    if (white_color == true) {
                        $("body").addClass("change-background");
                        setTimeout(function() {
                            $("body").removeClass("change-background");
                            $("body").removeClass("white-content");
                        }, 900);
                        white_color = false;
                    } else {
                        $("body").addClass("change-background");
                        setTimeout(function() {
                            $("body").removeClass("change-background");
                            $("body").addClass("white-content");
                        }, 900);

                        white_color = true;
                    }
                });

                $(".light-badge").click(function() {
                    $("body").addClass("white-content");
                });

                $(".dark-badge").click(function() {
                    $("body").removeClass("white-content");
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Javascript method's body can be found in assets/js/demos.js
            demo.initDashboardPageCharts();
        });
    </script>
    <!-- <script src="https://cdn.trackjs.com/agent/v3/latest/t.js"></script>
    <script>
        window.TrackJS &&
            TrackJS.install({
                token: "ee6fab19c5a04ac1a32a645abde4613a",
                application: "black-dashboard-free",
            });
    </script> -->

    <!-- add from stisla -->
    <!-- <script src="<?= base_url() ?>/assets/vendor/jquery/dist/jquery.min.js"></script> -->
    <script src="<?= base_url() ?>/assets/vendor/popper.js/dist/umd/popper.min.js"></script>
    <!-- <script src="<?= base_url() ?>/assets/vendor/bootstrap/dist/js/bootstrap.min.js"></script> -->
    <script src="<?= base_url() ?>/assets/vendor/jquery.nicescroll/dist/jquery.nicescroll.min.js"></script>
    <script src="<?= base_url() ?>/assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>/assets/vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>


    <!-- Template JS File -->
    <script src="<?= base_url() ?>/assets/js/scripts.js"></script>
    <script src="<?= base_url() ?>/assets/js/custom.js"></script>
    <script>
        function showSidebar() {
            var element = document.getElementById("change-sidebar");
            if (element.classList.contains("sidebar-mini") === true) {
                element.classList.remove("sidebar-mini");
            } else {
                element.classList.add("sidebar-mini");
            }
        }

        function closeAlert() {
            $(".alert").alert('close');
        }
    </script>

    <?= $this->renderSection('select-olt') ?>

    <?= $this->renderSection('user-manage'); ?>


</body>

</html>