<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) )
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isGetRequest() ) {

    $countries = getGlossaryData( $pdo );

    if ( isset( $_SESSION['username'] ) )
        $countries = addGradesToGlossary( $pdo, $countries );

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

    if ( isset( $_GET['sort'] ) ) {
        $_SESSION['sort'] = $_GET['sort'];
        // list( $direction, $col ) = explode( '_', $sort ); resulted in errrors
        $dir_col = explode( '_', $_SESSION['sort'] );
        $direction = $dir_col[0];
        $col = $dir_col[1];
        $countries = sortGlossary( $direction, $col, $countries );
    } else {
        $sort = FALSE;
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
        <form class="input-group float-end p-3 me-2 w-50 rounded" 
            method="get" action="glossary.php">
            <input type="hidden" name="sort" value="<?= $_SESSION['sort'] ?>">
            <input id="search" class="form-control" type="text" name="search"
                placeholder="Search country or capital" autofocus value="<?= $search ?>">
            <button type="submit" class="btn search-btn">
                <i class="fa fa-search"></i>
            </button>
            <a href="glossary.php" class="btn search-btn py-3">
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