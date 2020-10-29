<form class="search-form" role="search" method="get" id="searchform" action="<?php echo home_url( '/' ) ?>" >
	<input class="search-input" type="text" value="<?php echo get_search_query() ?>" placeholder="Поиск" name="s" id="s" />
    <button type="submit" id="searchsubmit"></button>
</form>