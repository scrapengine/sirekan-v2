<div class="sidebar">

    <div class="sidebar-wrapper ps ps--active-x ps--active-y">
        <div class="logo">
            <a href="javascript:void(0)" class="simple-text logo-mini">
                CT
            </a>
            <a href="javascript:void(0)" class="simple-text logo-normal">
                Creative Tim
            </a>
        </div>
        <ul class="nav">
            <li>
                <a href="<?= base_url(); ?>/home">
                    <i class="tim-icons icon-chart-pie-36"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li>
                <a data-toggle="collapse" href="#pagesWan">
                    <i class="fas fa-broadcast-tower"></i>
                    <p class="font-weight-bold">
                        WAN
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="pagesWan">
                    <ul class="nav">
                        <li>
                            <a href="<?= base_url(); ?>/wan/nodeb">
                                <span class="sidebar-mini-icon">N</span>
                                <span class="sidebar-normal"> NODE-B </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>/wan/fulfillment">
                                <span class="sidebar-mini-icon">F</span>
                                <span class="sidebar-normal"> FULFILLMENT </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>/wan/ont">
                                <span class="sidebar-mini-icon">O</span>
                                <span class="sidebar-normal"> ONT </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a data-toggle="collapse" href="#pagesAdditional">
                    <i class="tim-icons icon-molecule-40 font-weight-bold"></i>
                    <p class="font-weight-bold">
                        ADDITIONAL
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse" id="pagesAdditional">
                    <ul class="nav">
                        <li>
                            <a href="<?= base_url(); ?>/additional/sto">
                                <span class="sidebar-mini-icon">S</span>
                                <span class="sidebar-normal"> STO </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>/additional/olt">
                                <span class="sidebar-mini-icon">O</span>
                                <span class="sidebar-normal"> OLT </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>/additional/metro">
                                <span class="sidebar-mini-icon">M</span>
                                <span class="sidebar-normal"> METRO </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(); ?>/dummy">
                                <span class="sidebar-mini-icon">D</span>
                                <span class="sidebar-normal"> DUMMY </span>
                            </a>
                        </li>
                        <li>
                    </ul>
                </div>
            <li>
                <?php if (in_groups(['superadmin', 'admin'])) : ?>
                    <a href="<?= base_url('user/userlist'); ?>">
                        <i class="fas fa-users"></i>
                        <p>USERLIST</p>
                    </a>
                <?php endif; ?>
            </li>
        </ul>
        <div class="ps__rail-x" style="width: 80px; left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 59px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 553px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 494px;"></div>
        </div>
    </div>
</div>