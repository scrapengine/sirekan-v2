<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon">
            <i class="fas fa-solid fa-place-of-worship"></i>
        </div>
        <div class="sidebar-brand-text mx-3">App <sup>2</sup></div>
    </a>


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>WAN</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Components:</h6>
                <a class="collapse-item" href="buttons.html">Buttons</a>
                <a class="collapse-item" href="cards.html">Cards</a>
                <a class="collapse-item" href="<?= base_url('wan'); ?>">Data NODE-B</a>
                <a class="collapse-item" href="cards.html">OLO</a>
                <a class="collapse-item" href="buttons.html">Fullfilment WAN</a>
                <a class="collapse-item" href="cards.html">H3I</a>
                <a class="collapse-item" href="buttons.html">Data ONT</a>
                <a class="collapse-item" href="cards.html">Material</a>
                <a class="collapse-item" href="buttons.html">All NODE-B</a>
            </div>
        </div>
    </li>

    <?php if (in_groups('superadmin')) : ?>
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            User Management
        </div>

        <!-- Nav Item - User Profile -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin'); ?>">
                <i class="fas fa-solid fa-users"></i>
                <span>User List</span></a>
        </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        User Profile
    </div>

    <!-- Nav Item - My Profile -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('user'); ?>">
            <i class="fas fa-solid fa-user"></i>
            <span>My Profile</span></a>
    </li>

    <!-- Nav Item - Edit User -->
    <li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-user-edit"></i>
            <span>Edit User</span></a>
    </li>

    <hr class="sidebar-divider my">

    <!-- Nav Item - Logout -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('logout'); ?>">
            <i class=" fas fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>