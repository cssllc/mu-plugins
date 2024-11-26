<?php
/**
 * Add page for styles review.
 *
 * @todo add form elements
 * @todo add button elements
 */

defined( 'ABSPATH' ) || die();

/**
 * Class: CSSLLC_StyleGuide
 */
class CSSLLC_StyleGuide {

	/**
	 * Slug for styleguide page.
	 * @var string
	 */
	const SLUG = '_site-styleguide';

	/**
	 * Get instance.
	 *
	 * @return self
	 */
	static function instance() : self {
		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}

	/**
	 * Check if styleguide page.
	 *
	 * @return bool
	 */
	static function is_styleguide_page() : bool {
		global $wp;

		return static::SLUG === $wp->request;
	}

	/**
	 * Check if user can view styleguide.
	 *
	 * @uses current_user_can()
	 * @uses wp_get_environment_type()
	 * @return bool
	 */
	static function is_user_permitted() : bool {
		return (
			current_user_can( 'switch_themes' )
			|| 'production' !== wp_get_environment_type()
		);
	}

	/**
	 * Get styleguide markup.
	 *
	 * @return string
	 */
	static function get_styleguide_markup() : string {
		ob_start();

		do_action( 'cssllc_styleguide/before_markup' );
		?>

		<div data-styleguide-label="Headlines">
			<h1>Headline 1</h1><br />
			<h2>Headline 2</h2><br />
			<h3>Headline 3</h3><br />
			<h4>Headline 4</h4><br />
			<h5>Headline 5</h5><br />
			<h6>Headline 6</h6><br />
			<h1>Headline 1 with <br />a second line</h1><br />
			<h2>Headline 2 with <br />a second line</h2><br />
			<h3>Headline 3 with <br />a second line</h3><br />
			<h4>Headline 4 with <br />a second line</h4><br />
			<h5>Headline 5 with <br />a second line</h5><br />
			<h6>Headline 6 with <br />a second line</h6>
		</div>

		<div data-styleguide-label="Paragraphs">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent <a href="#">venenatis magna erat</a>, quis porttitor risus convallis eu. Phasellus nec aliquet libero. Proin consectetur sapien odio, et dignissim nisi volutpat vel. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi molestie, quam a feugiat gravida, dolor orci rhoncus nisi, a ornare mauris nisi id eros. Sed malesuada mauris elit, <a href="#">et consequat metus</a> volutpat laoreet. Sed iaculis ex eu egestas porta. Morbi sodales rhoncus erat, ac ullamcorper odio vulputate vehicula. Pellentesque finibus rhoncus risus, et rutrum augue euismod sit amet.</p>

			<p>Etiam vitae diam sed ipsum ultricies fermentum nec a nulla. Cras cursus metus venenatis aliquet mollis. Nulla a tempus dolor. Phasellus pellentesque orci eros, eget vestibulum magna sollicitudin at. Nullam quam magna, fringilla quis enim ac, pulvinar maximus lectus. Donec non nibh pharetra, laoreet neque sit amet, consectetur urna. Etiam arcu augue, scelerisque eu odio id, pellentesque tempus nunc. Donec convallis ultricies accumsan. Sed sit amet accumsan sapien, a consequat nulla.</p>

			<p>Maecenas varius purus id massa vehicula, ac porta sapien sagittis. Pellentesque scelerisque sit amet eros nec feugiat. Donec laoreet vel nunc ut mattis. Etiam vestibulum sapien sed ultrices mattis. In malesuada dapibus odio, non tincidunt tortor. In suscipit varius interdum. Morbi euismod ligula eu tempus tincidunt. Pellentesque rhoncus erat est, non rhoncus elit dictum id. Donec ac placerat nibh. Ut auctor urna sit amet luctus cursus. Praesent pellentesque tempor arcu. Donec ut est lobortis, commodo purus ut, efficitur lorem. In maximus tellus nisl.</p>

			<p>Cras sem nisl, semper id nisi vel, molestie posuere odio. Aenean sit amet congue felis. Vivamus ac purus at leo vulputate placerat. Cras porta cursus ipsum, eget scelerisque quam finibus eget. Maecenas pellentesque lacus ac finibus porttitor. Etiam imperdiet tortor eu purus sollicitudin viverra non ac nulla. Curabitur laoreet ipsum feugiat blandit fringilla. Cras porta venenatis rutrum. Integer consequat nisi elit. Quisque tempus egestas dui, in cursus orci scelerisque in. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae;</p>

			<p>Maecenas erat sem, porta in interdum at, congue sit amet enim. Maecenas ultricies, ligula et luctus tempor, arcu ipsum maximus neque, in imperdiet diam erat vel libero. Duis ut ligula elit. Nullam urna dui, dapibus eget tellus a, venenatis ultrices arcu. Quisque sit amet tristique ipsum. Sed interdum ultricies libero. Ut eget tellus eget erat efficitur sollicitudin. Morbi mollis in risus sed egestas. In sit amet ex urna.</p>
		</div>

		<div data-styleguide-label="Ordered List">
			<ol>
				<li>Ordered list item 1</li>
				<li>Ordered list item 2</li>
				<li>Ordered list item 3</li>
				<li>Ordered list item 4 <br />with second line</li>
				<li>Ordered list item 5 <br />with second line</li>
				<li>Ordered list item 6 <br />with second line</li>
				<li>Ordered list item 7 with ordered children
					<ol>
						<li>Ordered list item 7.1</li>
						<li>Ordered list item 7.2</li>
						<li>Ordered list item 7.3</li>
					</ol>
				</li>
				<li>Ordered list item 8 with unordered children
					<ul>
						<li>Ordered list item 8.1</li>
						<li>Ordered list item 8.2</li>
						<li>Ordered list item 8.3</li>
					</ul>
				</li>
			</ol>
		</div>

		<div data-styleguide-label="Unordered List">
			<ul>
				<li>Unordered list item 1</li>
				<li>Unordered list item 2</li>
				<li>Unordered list item 3</li>
				<li>Unordered list item 4 <br />with second line</li>
				<li>Unordered list item 5 <br />with second line</li>
				<li>Unordered list item 6 <br />with second line</li>
				<li>Unordered list item 7 with unordered children
					<ul>
						<li>Ordered list item 7.1</li>
						<li>Ordered list item 7.2</li>
						<li>Ordered list item 7.3</li>
					</ul>
				</li>
				<li>Unordered list item 8 with ordered children
					<ol>
						<li>Ordered list item 8.1</li>
						<li>Ordered list item 8.2</li>
						<li>Ordered list item 8.3</li>
					</ol>
				</li>
			</ul>
		</div>

		<div data-styleguide-label="Definition List">
			<dl>
				<dt>Definition term 1</dt>
				<dd>Definition description 1</dd>
				<dt>Definition term 2</dt>
				<dd>Definition description 2</dd>
				<dt>Definition term 3 <br />with a second line</dt>
				<dd>Definition description 3 <br />with a second line</dd>
				<dt>Definition term 4 <br />with a second line</dt>
				<dd>Definition description 4 <br />with a second line</dd>
			</dl>
		</div>

		<div data-styleguide-label="Table">
			<table>
				<thead>
					<tr>
						<td>Table Column 1</td>
						<td>Table Column 2</td>
						<td>Table Column 3</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Table Cell 1x1</th>
						<td>Table Cell 1x2</td>
						<td>Table Cell 1x3</td>
					</tr>
					<tr>
						<th>Table Cell 2x1</th>
						<td>Table Cell 2x2</td>
						<td>Table Cell 2x3</td>
					</tr>
					<tr>
						<th>Table Cell 3x1</th>
						<td>Table Cell 3x2</td>
						<td>Table Cell 3x3</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td>Table Column 1</td>
						<td>Table Column 2</td>
						<td>Table Column 3</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<div data-styleguide-label="Blockquote">
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<blockquote>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</blockquote>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
		</div>
		<div data-styleguide-label="Block Images">
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<img src="https://source.unsplash.com/800x800" class="aligncenter" loading="lazy" />
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<img src="https://source.unsplash.com/300x300" class="aligncenter" loading="lazy" />
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<figure class="aligncenter">
				<img src="https://source.unsplash.com/800x801" loading="lazy" />
				<figcaption>Caption for the above image <br />with a second line.</figcaption>
			</figure>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<figure class="aligncenter">
				<img src="https://source.unsplash.com/300x301" class="aligncenter" loading="lazy" />
				<figcaption>Caption for the above image.</figcaption>
			</figure>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
		</div>

		<div data-styleguide-label="Inline Images">
			<p>
				<img src="https://source.unsplash.com/200x200" class="alignleft" loading="lazy" />
				Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<p>
				<img src="https://source.unsplash.com/200x201" class="alignright" loading="lazy" />
				Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<p>
				<figure class="alignleft">
					<img src="https://source.unsplash.com/200x202" loading="lazy" />
					<figcaption>Caption for the above, left-aligned image <br />with a second line.</figcaption>
				</figure>
				Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<p>
				<figure class="alignright">
					<img src="https://source.unsplash.com/200x203" loading="lazy" />
					<figcaption>Caption for the above, right-aligned image <br />with a second line.</figcaption>
				</figure>
				Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
		</div>

		<div data-styleguide-label="Sample Content">
			<h1>Headline 1 <br />with second line</h1>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h2>Headline 2 <br />with second line</h2>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<ul>
				<li>Unordered list item 1</li>
				<li>Unordered list item 2</li>
				<li>Unordered list item 3</li>
				<li>Unordered list item 4 <br />with second line</li>
				<li>Unordered list item 5 <br />with second line</li>
				<li>Unordered list item 6 <br />with second line</li>
			</ul>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h3>Headline 3 <br />with second line</h3>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<blockquote>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</blockquote>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h4>Headline 4 <br />with second line</h4>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<ol>
				<li>Ordered list item 1</li>
				<li>Ordered list item 2</li>
				<li>Ordered list item 3</li>
				<li>Ordered list item 4 <br />with second line</li>
				<li>Ordered list item 5 <br />with second line</li>
				<li>Ordered list item 6 <br />with second line</li>
			</ol>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h5>Headline 5 <br />with second line</h5>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h6>Headline 6 <br />with second line</h6>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
		</div>

		<div data-styleguide-label="Alt Sample Content">
			<h1>Headline 1 <br />with second line</h1>
			<h2>Headline 2 <br />with second line</h2>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h2>Headline 2 <br />with second line</h2>
			<h3>Headline 3 <br />with second line</h3>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h3>Headline 3 <br />with second line</h3>
			<h4>Headline 4 <br />with second line</h4>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h4>Headline 4 <br />with second line</h4>
			<h5>Headline 5 <br />with second line</h5>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
			<h5>Headline 5 <br />with second line</h5>
			<h6>Headline 6 <br />with second line</h6>
			<p>Lorem ipsum dolor sit amet, test link adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>
		</div>

		<?php
		do_action( 'cssllc_styleguide/after_markup' );

		return ob_get_clean();
	}

	/**
	 * Print styleguide markup.
	 *
	 * @uses static::get_styleguide_markup()
	 */
	static function print_styleguide() : void {
		echo static::get_styleguide_markup();
	}

	/**
	 * Construct.
	 */
	protected function __construct() {

		add_action( 'init', array( $this, 'action__init' ) );

	}

	/**
	 * Action: init
	 *
	 * Add other hooks if user permitted.
	 *
	 * @uses static::is_user_permitted()
	 */
	function action__init() {
		if ( !static::is_user_permitted() )
			return;

		add_action( 'template_redirect', array( $this, 'action__template_redirect' ) );
		add_action( 'wp_head', array( $this, 'action__wp_head' ) );
		add_action( 'admin_bar_menu', array( $this, 'action__admin_bar_menu' ), 99 );

		add_filter( 'wp_resource_hints', array( $this, 'filter__wp_resource_hints' ), 10, 2 );
		add_filter( 'document_title_parts', array( $this, 'filter__document_title_parts' ) );
	}

	/**
	 * Action: template_redirect
	 *
	 * Check if styleguide URL and print markup.
	 *
	 * @uses static::is_styleguide_page()
	 * @uses get_header()
	 * @uses static::print_styleguide()
	 * @uses get_footer()
	 */
	function action__template_redirect() : void {
		if ( !static::is_styleguide_page() )
			return;

		status_header( 200 );

		get_header();
		static::print_styleguide();
		get_footer();

		exit;
	}

	/**
	 * Action: wp_head
	 *
	 * Print styles for styleguide.
	 */
	function action__wp_head() : void {
		?>

		<style>
			div[data-styleguide-label] {
				position: relative;
				margin: 0 auto 50px;
			}

			div[data-styleguide-label]::before {
				content: attr( data-styleguide-label );
				position: sticky;
				top: 0;
				display: block;
				width: 100%;
				padding: 10px 0;
				margin-bottom: 20px;
				text-transform: uppercase;
				font-family: sans-serif;
				font-size: 0.7em;
				background-color: #FFF;
				color: #999;
				border-bottom: 2px solid #EEE;
			}

				body.admin-bar div[data-styleguide-label]::before {
					top: 32px;
				}
		</style>

		<?php
	}

	/**
	 * Action: admin_bar_menu
	 *
	 * Add link to toolbar.
	 *
	 * @param object $bar
	 */
	function action__admin_bar_menu( object $bar ) : void {
		$bar->add_menu( array(
			'id' => 'cssllc-styleguide',
			'title' => 'View Site Styleguide',
			'parent' => 'site-name',
			'href' => home_url( static::SLUG ),
		) );
	}

	/**
	 * Filter: wp_resource_hints
	 *
	 * Preconnect to source.unsplash.com.
	 *
	 * @param string[] $urls
	 * @param string $rel
	 * @return string[]
	 */
	function filter__wp_resource_hints( array $urls, string $rel ) : array {
		if ( 'preconnect' !== $rel )
			return $urls;

		$urls[] = 'https://source.unsplash.com';

		return $urls;
	}

	/**
	 * Filter: document_title_parts
	 *
	 * Change document title.
	 *
	 * @param string[] $title_parts
	 * @return array
	 */
	function filter__document_title_parts( array $title_parts ) : array {
		if ( !static::is_styleguide_page() )
			return $title_parts;

		$title_parts['title'] = 'Internal Styleguide';
		return $title_parts;
	}

}

CSSLLC_StyleGuide::instance();