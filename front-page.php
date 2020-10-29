<?php get_header(); ?>

<main class="front-page-header">
	<div class="container">
		<div class="hero">
			<div class="hero-left">
				<?php
					global $post;

					$myposts = get_posts([ 
						'numberposts' => 1,
						'category_name' => 'css, javascript, html, web-design'
					]);
						// проверка на наличие постов в базе данных
					if( $myposts ){
						foreach( $myposts as $post ){
							setup_postdata( $post );
				?>
				<img src="<?php
					if ( has_post_thumbnail() ) {
					echo get_the_post_thumbnail_url() ?>" alt="<?php the_title()?>" class="post-thumb">
					<?php
					}
					else {
						echo get_template_directory_uri() . '/assets/images/image-default.png" class="post-thumb">';
					}
					?>
				<?php $author_id = get_the_author_meta('ID')?>
				<a href="<?php echo get_author_posts_url($author_id); ?>" class="author">
					<img src="<?php echo get_avatar_url($author_id, array('default'=>'gravatar_default')); ?>" class="author-photo" alt="photo">
					<div class="author-bio">
						<span class="author-name"><?php the_author(); ?></span>
						<span class="author-rank">Разработчик</span>
					</div>
				</a>
				<div class="post-text">
					<?php 
					foreach (get_the_category() as $category) {
						printf('<a href="%s" class="category-link %s">%s</a>',
						get_category_link($category),
						$category -> slug,
						$category -> name,
					);
					}
					?>
					<h2 class="post-title"><?php echo wp_trim_words(get_the_title(), 5); ?></h2>
					<a href="<?php the_permalink(); ?>" class="more">Читать далее</a>
				</div>
				<?php 
						}
					} else {
						?> <p>Постов не найдено</p> <?php
					}
					wp_reset_postdata(); // Сбрасываем $post
				?>   
			</div>
			<!-- /.hero-left -->
			<div class="hero-right">
				<h3 class="recommended">рекомендуем</h3>
				<?php
					global $post;

					$myposts = get_posts([ 
						'numberposts' => 5,
						'category_name' => 'css, javascript, html, web-design'
					]);
						// проверка на наличие постов в базе данных
					if( $myposts ){
						foreach( $myposts as $post ){
							setup_postdata( $post );
				?>
				<ul class="posts">
					<li class="posts-item">
					<?php 
					foreach (get_the_category() as $category) {						
						printf('<a href="%s" class="category-link %s">%s</a>',
						esc_url( get_category_link($category) ),
						esc_html( $category -> slug ),
						esc_html( $category -> name ),
					);
					}
					?>
						<a href="<?php the_permalink(); ?>"><h4 class="posts-title"><?php echo wp_trim_words(get_the_title(), 8, ' ...'); ?></h4></a>						
					</li>
				</ul>
				<?php 
						}
					} else {
						?> <p>Постов не найдено</p> <?php
					}
					wp_reset_postdata(); // Сбрасываем $post
				?>  
			</div>
			<!-- /.hero-right -->
		</div>
		<!-- /.hero -->
	</div>
	<!-- /.container -->
</main>
<div class="container">
	<div class="articles">
	<?php
		global $post;

		$myposts = get_posts([ 
			'numberposts' => 4,
			'category_name' => 'articles'
		]);
			// проверка на наличие постов в базе данных
		if( $myposts ){
			foreach( $myposts as $post ){
				setup_postdata( $post );
	?>
		<ul class="articles-list">
			<li class="articles-item">
				<a href="<?php the_permalink(); ?>" class="articles-permalink">
					<h4 class="articles-title"><?php echo wp_trim_words(get_the_title(), 5, ' ...');?></h4>
					<img src="<?php
					if ( has_post_thumbnail() ) {
					echo get_the_post_thumbnail_url() ?>" alt="<?php the_title()?>" width="65" height="65">
					<?php
					}
					else {
						echo get_template_directory_uri() . '/assets/images/image-default.png" width="65" height="65">';
					}
					?>	
				</a>               						
			</li>
		</ul>
	<?php 
			}
		} else {
			?> <p>Постов не найдено</p> <?php
		}
		wp_reset_postdata(); // Сбрасываем $post
	?>
	</div>
	<!-- /.articles -->
	<div class="main-grid">
		<div class="articles-grid">
		<?php		
			global $post;

			$query = new WP_Query( [
			'posts_per_page' => 7, // получаем 7 постов
			'tag' => 'popular',	  // с тегами "популярное"
			] );

			if ( $query->have_posts() ) {
			$count = 0;
			while ( $query->have_posts() ) {
			$query->the_post();
			$count++;
		?>
		<?php 
			switch ($count) {
				// выводим первый пост
				case '1':
		?>					
			<ul class="articles-grid-list articles-grid-list-1">
				<li class="articles-grid-item">
					<a href="<?php the_permalink(); ?>" class="articles-grid-permalink">
						<span class="articles-grid-category"><?php $category = get_the_category(); echo $category[0]->name;?></span>
						<h4 class="articles-grid-title"><?php echo get_the_title();?></h4>
						<p><?php echo wp_trim_words(get_the_excerpt(), 20, ' ...'); ?></p>
						<div class="articles-grid-info">
							<div class="articles-grid-author">
								<?php $author_id = get_the_author_meta('ID')?>
								<img src="<?php echo get_avatar_url($author_id, array('default'=>'gravatar_default')); ?>" alt="avatar" width="30" height="30" class="author-thumb">
								<span class="author-name">
								<strong><?php the_author(); ?></strong> : <?php echo get_the_author_meta('description')?>
								</span>
							</div>
							<div class="articles-grid-comments">
								<svg class="icon-comment" width="15" height="15" fill="#BCBFC2">
									<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#comment"></use>
								</svg>
								<span class="comments-count"><?php comments_number( '0', '1', '%' ); ?></span>
							</div>
						</div>
					</a>             						
				</li>
			</ul>
		<?php
			break;

			//выводим  второй пост
			case '2':
		?>
			<ul class="articles-grid-list articles-grid-list-2">
				<li class="articles-grid-item">
					<img src="<?php
					if ( has_post_thumbnail() ) {
					echo get_the_post_thumbnail_url() ?>" alt="<?php the_title()?>" class="articles-grid-thumb">
					<?php
					}
					else {
						echo get_template_directory_uri() . '/assets/images/image-default.png" class="articles-grid-thumb">';
					}
					?>	
					<a href="<?php the_permalink(); ?>" class="articles-grid-permalink">
						<span class="tag">
							<?php 
							$tagsname = get_the_tags();
							if ($tagsname) {
								echo $tagsname[0]->name . ' ';
							}
							?>
						</span>
						<span class="articles-grid-category"><?php $category = get_the_category(); echo $category[0]->name;?></span>
						<h4 class="articles-grid-title"><?php echo wp_trim_words(get_the_title(), 5);?></h4>
						<div class="articles-grid-author">
							<img src="<?php echo get_avatar_url($author_id, array('default'=>'gravatar_default')); ?>" alt="avatar" width="45" height="45" class="author-thumb">
							<div class="author-info">
								<span class="name"><?php the_author(); ?></span>
								<div class="date"><?php the_time('j F'); ?></div>
								<div class="comments">
									<svg class="icon-comment" width="15" height="15" fill="#fff">
										<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#comment"></use>
									</svg>
									<span class="comments-count"><?php comments_number( '0', '1', '%' ); ?></span>
								</div>
								<div class="likes">
									<svg class="icon-likes" width="15" height="15" fill="#fff">
										<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#heart"></use>
									</svg>
									<span class="likes-count"><?php comments_number( '0', '1', '%' ); ?></span>
								</div>
							</div>
						</div>
						
					</a>               						
				</li>
			</ul>
		<?php
			break;
			//выводим 3 пост
			case '3':
		?>
			<ul class="articles-grid-list articles-grid-list-3">
				<li class="articles-grid-item">
					<a href="<?php the_permalink(); ?>" class="articles-grid-permalink">
					<img src="<?php
					if ( has_post_thumbnail() ) {
					echo get_the_post_thumbnail_url() ?>" alt="<?php the_title()?>" class="articles-grid-thumb">
					<?php
					}
					else {
						echo get_template_directory_uri() . '/assets/images/image-default.png" class="articles-grid-thumb">';
					}
					?>	
						<h4 class="articles-grid-title"><?php echo wp_trim_words(get_the_title(), 5);?></h4>
					</a>               						
				</li>
			</ul>
		
		<?php
			break;
			// выводим остальные посты
			default:
		?>
			<ul class="articles-grid-list articles-grid-list-default">
				<li class="articles-grid-item">
					<a href="<?php the_permalink(); ?>" class="articles-grid-permalink">
						<h4 class="articles-grid-title"><?php echo wp_trim_words(get_the_title(), 3);?></h4>
						<p><?php echo wp_trim_words(get_the_excerpt(), 5, ' ...'); ?></p>
						<div class="articles-grid-date"><?php the_time('j F'); ?></div>
					</a>               						
				</li>
			</ul>
		<?php
			break;
			}
		?>
		<?php 
			}
			} else {
			// Постов не найдено
			}

			wp_reset_postdata(); // Сбрасываем $post
		?>
		</div>
	<!-- /.articles-grid -->	
		<?php get_sidebar( ); ?>  <!-- выводится сайдбар в теге <aside> -->	
	</div>
	<!-- /.main-grid -->
</div>
<!-- /.container -->
<div class="investigation" style="background: linear-gradient(0deg, rgba(64, 48, 61, 0.5), rgba(64, 48, 61, 0.35)), url(<?php echo get_the_post_thumbnail_url() ?>) no-repeat center center">
	<div class="container">
	<?php		
		global $post;

		$query = new WP_Query( [
			'posts_per_page' => 1,
			'tag' => 'investigations'
		] );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
	?>
		<h2 class="post-title"><?php echo wp_trim_words(get_the_title(), 8); ?></h2>
		<a href="<?php the_permalink(); ?>" class="more">Читать статью</a>
	<?php 
			}
		} else {
			// Постов не найдено
		}
		wp_reset_postdata(); // Сбрасываем $post
	?>
	</div>
</div>
<!-- /.investigation -->
<div class="container">
	<div class="secondary-grid">
		<div class="articles-secondary">
			<?php
				global $post;

				$myposts = get_posts([ 
					'numberposts' => 6,
					'category_name' => 'selections, news, opinions, hotnews',
					'order' => 'ASC'
				]);
					// проверка на наличие постов в базе данных
				if( $myposts ){
					foreach( $myposts as $post ){
						setup_postdata( $post );
			?>
				<ul class="articles-secondary-list">
					<li class="articles-secondary-item">
						<a href="<?php the_permalink(); ?>" class="articles-secondary-permalink">	
							<img src="<?php
							if ( has_post_thumbnail() ) {
							echo get_the_post_thumbnail_url() ?>" alt="<?php the_title()?>" class="articles-secondary-thumb">
							<?php
							}
							else {
								echo get_template_directory_uri() . '/assets/images/image-default.png" class="articles-secondary-thumb">';
							}
							?>	
							<div class="articles-secondary-content">
								<div class="articles-secondary-text">
									<span class="articles-secondary-category"><?php
									 foreach (get_the_category() as $category) {						
										printf('<span class="category-link %s">%s</span>',
										esc_html( $category -> slug ),
										esc_html( $category -> name ),
									);
									}?>
									 </span>
									<h4 class="articles-secondary-title"><?php echo mb_strimwidth(get_the_title(), 0, 65, "...");?></h4>
									<p><?php echo wp_trim_words(get_the_excerpt(), 18, ' ...'); ?></p>
								</div>
								<div class="articles-secondary-info">
									<div class="date"><?php the_time('j F'); ?></div>
									<div class="comments">
										<svg class="icon-comment" width="15" height="15" fill="#BCBFC2">
											<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#comment"></use>
										</svg>
										<span class="comments-count"><?php comments_number( '0', '1', '%' ); ?></span>
									</div>
									<div class="likes">
										<svg class="icon-likes" width="15" height="15" fill="#BCBFC2">
											<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#heart"></use>
										</svg>
										<span class="likes-count"><?php comments_number( '0', '1', '%' ); ?></span>
									</div>
								</div>
							</div>
							<svg class="icon-bookmark" width="15" height="15" fill="#BCBFC2">
							<use xlink:href="<?php echo get_template_directory_uri()?>/assets/images/sprite.svg#bookmark"></use>
							</svg>
						</a>             						
					</li>
				</ul>
			<?php 
					}
				} else {
					?> <p>Постов не найдено</p> <?php
				}
				wp_reset_postdata(); // Сбрасываем $post
			?>
		</div>
		<!-- /.articles-secondary -->
		<?php get_sidebar('recent') ?>
	</div>
	<!-- /.secondary-grid -->
</div>
<!-- /.container -->
<div class="special">
	<div class="container">
		<div class="special-grid">
			<div class="photo-report">
			<?php
				foreach (get_the_category() as $category) {						
				printf('<span class="category-link %s">%s</span>',
				esc_html( $category -> slug ),
				esc_html( $category -> name ),
				);}
			?>
			</div>
			<div class="other"></div>
		</div>
	</div>
</div>
<br>
<br>
<br>
<br>
<?php get_footer();

