<nav id="falseNavbar" class="sidenav bg-light">
            
<?php 
    if ( isset( $_SESSION['username'] ) ) {
        echo '<div class="card card-body m-3 p-4 bg-light border-5 border-white rounded-4">
            <div id="userId" class="card-title fs-3">
                <i class="fa-regular fa-user-circle"></i> &nbsp;' 
                    . $_SESSION['username'] . 
                '</div>
            <div class="card-text pt-2">
                &nbsp;
            </div>
        </div>';
    }
?>

</nav>