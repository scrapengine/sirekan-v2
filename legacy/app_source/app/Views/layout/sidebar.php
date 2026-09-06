<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="<?= site_url(); ?>">SIREKAN</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="<?= site_url(); ?>">SR</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item">
                <a href="<?= site_url('home'); ?>"><i class="fas fa-fire"></i><span>Dashboard</span></a>
            </li>
            <li class="menu-header">Divisi</li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-broadcast-tower"></i> <span>WAN</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= site_url('wan/map'); ?>">MAPS</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/nodeb'); ?>">NODE-B</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/olo'); ?>">OLO</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/assurance'); ?>">ASSURANCE</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/fulfillment'); ?>">FULFILLMENT</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/ont'); ?>">ONT</a></li>
                    <li><a class="nav-link" href="<?= site_url('wan/allnodeb'); ?>">ALL NODE-B</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-network-wired"></i> <span>CCAN</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= site_url('ccan/assurance'); ?>">ASSURANCE</a></li>
                    <li><a class="nav-link" href="<?= site_url('ccan/fulfillment'); ?>">FULFILLMENT</a></li>
                </ul>
            </li>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-wifi"></i></i> <span>WIFI</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= site_url('wifi/assurance'); ?>">ASSURANCE</a></li>
                    <li><a class="nav-link" href="<?= site_url('wifi/fulfillment'); ?>">FULFILLMENT</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-th"></i> <span>ADDITIONAL</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url(); ?>/additional/sto">STO</a></li>
                    <li><a class="nav-link" href="<?= base_url(); ?>/additional/naker">NAKER</a></li>
                    <li><a class="nav-link" href="<?= base_url(); ?>/additional/metro">METRO</a></li>
                    <li><a class="nav-link" href="<?= base_url(); ?>/additional/olt">OLT</a></li>
                </ul>
            </li>

            <?php if (in_groups(['superadmin', 'admin'])) : ?>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <li class="menu-header">User Management</li>

                <!-- Nav Item - User Profile -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('user/userlist'); ?>">
                        <i class="fas fa-solid fa-users"></i>
                        <span>User List</span></a>
                </li>
            <?php endif; ?>

            <hr class="sidebar-divider">

            <!-- Nav Item - Logout -->
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('logout'); ?>">
                    <i class=" fas fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </li>

    </aside>
</div>