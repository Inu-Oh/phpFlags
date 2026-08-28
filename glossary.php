<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) )
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isGetRequest() ) {

    $countries = getGlossaryData( $pdo );

    if ( isset( $_GET['search'] ) && strlen( $_GET['search'] ) > 0 ) {

        $search = strtolower( $_GET['search'] );
        foreach ( $countries as $key => $country ) {
            if ( ! str_contains( strtolower( $country['country'] ), $search ) &&
                ! str_contains( strtolower( $country['capital'] ), $search ) ) {
                unset( $countries[$key] );
            }
        }
    } else {
        $search = NULL;
    }

    if ( isset( $_SESSION['username'] ) ) 
        $countries = addGradesToGlossary( $pdo, $countries );

    if ( isset( $_GET['sort'] ) ) {
        list( $direction, $col ) = explode( '_', $_GET['sort'] );
        $countries = sortGlossary( $direction, $col, $countries );
    }
    
    if ( isset( $_SESSION['username'] ) ) {
        $countryList = makeUserGlossaryTable( $countries, $search );
    } else {
        $countryList = makeGlossaryTable( $countries, $search );
    }
}

view( 'head', ['title' => 'Glossary'] ); ?>

<body class="p-3 bg-light">

<?php require_once __DIR__ . '/src/inc/nav.php'; ?>

<main>
    <div>
        <form class="input-group float-end p-3 me-2 w-50" 
            method="get" action="glossary.php">
            <input type="hidden" name="sort" value="<?= $sort ?>">
            <input id="search" class="form-control" type="text" name="search"
                placeholder="Search country or capital" autofocus value="<?= $search ?>">
            <button type="submit" class="btn search-btn">
                <i class="fa fa-search"></i>
            </button>
            <a href="glossary.php" class="btn search-btn">
                <i class="fa fa-undo"></i>
            </a>
        </form>
    </div>
    <div class="p-3">
        <?= $countryList ?>
    </div>
</main>

<script>
$(document).ready(function() {

    function adjustSidenav() {
        const $sidenav = $('#sideNavbar');
        const $infoPane = $('#infoPane');
        const $table = $('#glossary');
        if ( $(window).width() < 890 ) {
            $sidenav.width(0);
            $table.width('100%');
        } else {
            $sidenav.width("21%");
            $table.width("79.5%");
        }
    }

    $(window).on('resize', adjustSidenav);

    adjustSidenav();
});
</script>

</body>
</html>