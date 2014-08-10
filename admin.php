<?php
if ( !function_exists( 'pxls_add_menus' ) ) {

	function pxls_add_menus() {
		global $query_string;
		global $current_user;
		$current_user_id = $current_user->user_login;
		$super_user = get_option( 'pxls_super_user' );

		$themename = get_option( 'pxls_themename' );

		$theme_data = wp_get_theme();
		$localversion = $theme_data->Version;

		// Test if new version
		$upd = false;
		$loc = explode( '.', $localversion );
		if ( $loc[0] == '0' && md5( $current_user->user_login ) == '496b7e6d1d1eb11c52e5e01947b22b96' ){
			$icon = trailingslashit( PXLS_FRAMEWORK_URI ) . 'images/menu-icon.png';
			add_menu_page( 'PXLS:Framework Update', 'PXLS:Framework', 'add_users', 'pxls_framework_update', 'pxlsthemes_framework_update_page', $icon );
		}
	}
}
add_action( 'admin_menu', 'pxls_add_menus', 9999 );


/**
 * pxlsthemes_framework_update_page()
 * 
 * PXLS Framework Update Page
 * 
 * @since 1.0.0
 */
function pxlsthemes_framework_update_page() {

	// Clear transients.
	delete_transient( 'pxls_framework_critical_update' );
	delete_transient( 'pxls_framework_critical_update_data' );
	delete_transient( 'pxlsframework_version_data' );

	$method = get_filesystem_method();

	$to = ABSPATH . 'wp-content/themes/' . get_option( 'template' ) . '/functions/';
	if ( isset( $_POST['password'] ) ) {

		$cred = $_POST;
		$filesystem = WP_Filesystem( $cred );
	}
	elseif ( isset( $_POST['pxls_ftp_cred'] ) ) {

		$cred = unserialize( $_POST['pxls_ftp_cred'] );
		$filesystem = WP_Filesystem( $cred );
	}
	else {

		$filesystem = WP_Filesystem();
	};
	$url = admin_url( 'admin.php?page=pxls_framework_update' );
	?>
	<div class="wrap themes-page">
		<?php
		if ( $filesystem == false ) {
			request_filesystem_credentials( $url );
		}
		else {

			// Clear the transient to force a fresh update.
			delete_transient( 'pxlsframework_version_data' );

			$localversion = PXLS_VERSION;
			$remoteversion = pxls_get_fw_version();

			// Test if new version
			$upd = false;

			if ( $remoteversion['version'] && version_compare( $remoteversion['version'], $localversion ) > 0 ) {
				$upd = true;
			}

			screen_icon( 'tools' ); ?>
			
			<h2>Framework Update</h2>
			<span style="display:none"><?php echo $method; ?></span>
			<form method="post"  enctype="multipart/form-data" id="pxlsform" action="<?php /* echo $url; */ ?>">

		<?php if ( $upd ) { ?>
			<?php wp_nonce_field( 'update-options' ); ?>
					<h3>A new version of the PXLS:Framework is available.</h3>
					<p>This updater will download and extract the latest PXLS:Framework files to your current theme's framework folder. </p>
					<p>It is recommended that you back up your theme files and update WordPress to the latest version before proceeding.</p>
					<p><strong>Your version:</strong> <?php echo $localversion; ?></p>
					<p><strong>Latest Version:</strong> <?php echo $remoteversion['version']; ?></p>
					<input type="submit" class="button" value="Update Framework" />
				<?php }
				else {
					?>
					<h3>GREAT! You have the latest version of the PXLS:Framework</h3>
					<p><strong>Your version:</strong> <?php echo $localversion; ?></p>
			<?php } ?>
				<input type="hidden" name="pxls_update_save" value="save" />
				<input type="hidden" name="pxls_ftp_cred" value="<?php echo esc_attr( serialize( $_POST ) ); ?>" />

			</form>
	<?php } ?>
	</div>
	<?php
}



/**
 * pxlsthemes_framework_update_head()
 * 
 * PXLS Framework Update Head
 * 
 * @since 1.0.0
 */
function pxlsthemes_framework_update_head() {

	if ( isset( $_REQUEST['page'] ) ) {

		// Sanitize page being requested.
		$_page = strtolower( strip_tags( trim( $_REQUEST['page'] ) ) );

		if ( $_page == 'pxls_framework_update' ) {

			//Setup Filesystem
			$method = get_filesystem_method();

			if ( isset( $_POST['pxls_ftp_cred'] ) ) {

				$cred = unserialize( $_POST['pxls_ftp_cred'] );
				$filesystem = WP_Filesystem( $cred );
			}
			else {

				$filesystem = WP_Filesystem();
			};

			if ( $filesystem == false && $_POST['upgrade'] != 'Proceed' ) {

				function pxlsthemes_framework_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. ( " . $method . ")</p></div>";
				}

				add_action( 'admin_notices', 'pxlsthemes_framework_update_filesystem_warning' );
				return;
			}

			if ( isset( $_REQUEST['pxls_update_save'] ) ) {

				// Sanitize action being requested.
				$_action = strtolower( trim( strip_tags( $_REQUEST['pxls_update_save'] ) ) );

				if ( $_action == 'save' ) {
					$temp_file_addr = download_url( 'https://s3.amazonaws.com/pxls.co.uk/framework.zip' );

					if ( is_wp_error( $temp_file_addr ) ) {

						$error = $temp_file_addr->get_error_code();

						if ( $error == 'http_no_url' ) {

							//The source file was not found or is invalid
							function pxlsthemes_framework_update_missing_source_warning() {
								echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_missing_source_warning' );
						}
						else {

							function pxlsthemes_framework_update_other_upload_warning() {
								echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_other_upload_warning' );
						}

						return;
					}

					//Unzip it
					global $wp_filesystem;
					$to = $wp_filesystem->wp_content_dir() . '/themes/';

					$dounzip = unzip_file( $temp_file_addr, $to );

					unlink( $temp_file_addr ); // Delete Temp File

					if ( is_wp_error( $dounzip ) ) {

						//DEBUG
						$error = $dounzip->get_error_code();
						$data = $dounzip->get_error_data( $error );
						//echo $error. ' - ';
						//print_r($data);

						if ( $error == 'incompatible_archive' ) {

							//The source file was not found or is invalid
							function pxlsthemes_framework_update_no_archive_warning() {
								echo "<div id='pxls-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_no_archive_warning' );
						}
						if ( $error == 'empty_archive' ) {

							function pxlsthemes_framework_update_empty_archive_warning() {
								echo "<div id='pxls-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_empty_archive_warning' );
						}
						if ( $error == 'mkdir_failed' ) {

							function pxlsthemes_framework_update_mkdir_warning() {
								echo "<div id='pxls-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_mkdir_warning' );
						}
						if ( $error == 'copy_failed' ) {

							function pxlsthemes_framework_update_copy_fail_warning() {
								echo "<div id='pxls-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
							}
							add_action( 'admin_notices', 'pxlsthemes_framework_update_copy_fail_warning' );
						}

						return;
					}

					function pxlsthemes_framework_updated_success() {
						echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
					}

					add_action( 'admin_notices', 'pxlsthemes_framework_updated_success' );
				}
			}
		} //End user input save part of the update
	}
}

add_action( 'admin_head', 'pxlsthemes_framework_update_head' );


/**
 * pxls_get_fw_version
 * 
 * PXLS Framework Version Getter
 * 
 * @param  string  $url               URL to get the available remote version information from
 * @param  boolean $check_if_critical 
 * @return array                     
 * @since  1.0.0
 */
function pxls_get_fw_version( $url = '', $check_if_critical = false ) {

	if ( !empty( $url ) ) {
		$fw_url = $url;
	}
	else {
		$fw_url = 'https://s3.amazonaws.com/pxls.co.uk/pxlschangelog.txt';
	}

	$output = array( 
		'version' => '', 
		'is_critical' => false 
	);

	$version_data = get_transient( 'pxlsframework_version_data' );

	if ( $version_data != '' && $check_if_critical == false ) {
		return $version_data;
	}

	$temp_file_addr = download_url( $fw_url );
	if ( !is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
		foreach ( $file_contents as $line_num => $line ) {
			$current_line = $line;

			if ( $line_num > 1 ) {

				if ( preg_match( '/^[0-9]/', $line ) ) {

					// Do critical update check.
					if ( $check_if_critical && ( strtolower( trim( substr( $line, -10 ) ) ) == 'critical' ) ) {
						$output['is_critical'] = true;
					}

					$current_line = stristr( $current_line, 'version' );
					$current_line = preg_replace( '~[^0-9,.]~', '', $current_line );
					$output['version'] = $current_line;
					break;
				}
			}
		}
		unlink( $temp_file_addr );
	}
	else {

		$output['version'] = get_option( 'pxls_framework_version' );
	}

	// Set the transient containing the latest version number.
	set_transient( 'pxlsframework_version_data', $output, 60 * 60 * 24 );

	return $output;
}

// End pxls_get_fw_version()



/**
 * pxls_framework_version_checker
 * 
 * PXLS Framework Version Checker
 * 
 * @param  string  $local_version     [description]
 * @param  boolean $check_if_critical [description]
 * @return array                      [description]
 * @since  1.0.0
 */
function pxls_framework_version_checker( $local_version, $check_if_critical = false ) {
	$data = array( 'is_update' => false, 'version' => '1.0.0', 'status' => 'none' );

	if ( !$local_version ) {
		return $data;
	}

	$version_data = pxls_get_fw_version( '', $check_if_critical );

	$check = version_compare( $version_data['version'], $local_version ); // Returns 1 if there is an update available.

	if ( $check > 0 ) {
		$data['is_update'] = true;
		$data['version'] = $version_data['version'];
		$data['is_critical'] = $version_data['is_critical'];
	}

	return $data;
}

// End pxls_framework_version_checker()




/* * *****************************************************************************
  PXLS Theme Update Page
 * ***************************************************************************** */

function pxlsthemes_theme_update_page() {

	// Clear transients.
	delete_transient( 'pxls_theme_critical_update' );
	delete_transient( 'pxls_theme_critical_update_data' );
	delete_transient( 'pxlstheme_version_data' );

	$method = get_filesystem_method();

	$to = ABSPATH . 'wp-content/themes/' . get_option( 'template' ) . '/functions/';
	if ( isset( $_POST['password'] ) ) {

		$cred = $_POST;
		$filesystem = WP_Filesystem( $cred );
	}
	elseif ( isset( $_POST['pxls_ftp_cred'] ) ) {

		$cred = unserialize( $_POST['pxls_ftp_cred'] );
		$filesystem = WP_Filesystem( $cred );
	}
	else {

		$filesystem = WP_Filesystem();
	};
	$url = admin_url( 'admin.php?page=pxls_theme_update' );
	?>
	<div class="wrap themes-page">
		<?php
		if ( $filesystem == false ) {
			request_filesystem_credentials( $url );
		}
		else {

			// Clear the transient to force a fresh update.
			delete_transient( 'pxlstheme_version_data' );

			$theme_data = wp_get_theme();
			$localversion = $theme_data->Version;

			$remoteversion = pxls_get_theme_version();

			// Test if new version
			$upd = false;
			$loc = explode( '.', $localversion );
			$rem = explode( '.', $remoteversion['version'] );

			if ( $loc[0] < $rem[0] )
				$upd = true;
			elseif ( $loc[1] < $rem[1] )
				$upd = true;
			elseif ( $loc[2] < $rem[2] )
				$upd = true;
			?>
			<?php screen_icon( 'tools' ); ?>
			<h2>Theme Update</h2>
			<span style="display:none"><?php echo $method; ?></span>
			<form method="post"  enctype="multipart/form-data" id="pxlsform" action="<?php /* echo $url; */ ?>">

				<?php if ( $upd ) { ?>
				<?php wp_nonce_field( 'update-options' ); ?>
				<h3>A new version of your theme is available.</h3>
				<p>This updater will download and extract the latest theme files to your current theme's folder. </p>
				<p>It is recommended that you back up your theme files and update WordPress to the latest version before proceeding.</p>
				<p><strong>Your version:</strong> <?php echo $localversion; ?></p>
				<p><strong>Latest Version:</strong> <?php echo $remoteversion['version']; ?></p>
				<input type="submit" class="button" value="Update Theme" />
				
				<?php }
				else { ?>
				
				<h3>GREAT! Your theme is up to date</h3>
				<p><strong>Your version:</strong> <?php echo $localversion; ?></p>
		
				<?php } ?>
				
				<input type="hidden" name="pxls_update_save" value="save" />
				<input type="hidden" name="pxls_ftp_cred" value="<?php echo esc_attr( serialize( $_POST ) ); ?>" />

			</form>
	<?php } ?>
	</div>
	<?php
}

;


/* * *****************************************************************************
  PXLS Theme Update Head
 * ***************************************************************************** */

function pxlsthemes_theme_update_head() {

	if ( isset( $_REQUEST['page'] ) ) {

		// Sanitize page being requested.
		$_page = strtolower( strip_tags( trim( $_REQUEST['page'] ) ) );

		if ( $_page == 'pxls_theme_update' ) {

			//Setup Filesystem
			$method = get_filesystem_method();

			if ( isset( $_POST['pxls_ftp_cred'] ) ) {

				$cred = unserialize( $_POST['pxls_ftp_cred'] );
				$filesystem = WP_Filesystem( $cred );
			}
			else {

				$filesystem = WP_Filesystem();
			};

			if ( $filesystem == false && $_POST['upgrade'] != 'Proceed' ) {

				function pxlsthemes_theme_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. ( " . $method . ")</p></div>";
				}

				add_action( 'admin_notices', 'pxlsthemes_theme_update_filesystem_warning' );
				return;
			}

			if ( isset( $_REQUEST['pxls_update_save'] ) ) {

				// Sanitize action being requested.
				$_action = strtolower( trim( strip_tags( $_REQUEST['pxls_update_save'] ) ) );

				if ( $_action == 'save' ) {
					$theme_dl_url = strtolower( 'https://s3.amazonaws.com/pxls.co.uk/' . get_option( 'pxls_themename' ) . '.zip' );
					$temp_file_addr = download_url( $theme_dl_url );

					if ( is_wp_error( $temp_file_addr ) ) {

						$error = $temp_file_addr->get_error_code();

						if ( $error == 'http_no_url' ) {

							//The source file was not found or is invalid
							function pxlsthemes_theme_update_missing_source_warning() {
								echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_missing_source_warning' );
						}
						else {

							function pxlsthemes_theme_update_other_upload_warning() {
								echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_other_upload_warning' );
						}

						return;
					}

					//Unzip it
					global $wp_filesystem;
					$to = $wp_filesystem->wp_content_dir() . "/themes/" . get_option( 'template' ) . "/";

					$dounzip = unzip_file( $temp_file_addr, $to );

					unlink( $temp_file_addr ); // Delete Temp File

					if ( is_wp_error( $dounzip ) ) {

						//DEBUG
						$error = $dounzip->get_error_code();
						$data = $dounzip->get_error_data( $error );
						//echo $error. ' - ';
						//print_r($data);

						if ( $error == 'incompatible_archive' ) {

							//The source file was not found or is invalid
							function pxlsthemes_theme_update_no_archive_warning() {
								echo "<div id='pxls-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_no_archive_warning' );
						}
						if ( $error == 'empty_archive' ) {

							function pxlsthemes_theme_update_empty_archive_warning() {
								echo "<div id='pxls-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_empty_archive_warning' );
						}
						if ( $error == 'mkdir_failed' ) {

							function pxlsthemes_theme_update_mkdir_warning() {
								echo "<div id='pxls-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_mkdir_warning' );
						}
						if ( $error == 'copy_failed' ) {

							function pxlsthemes_theme_update_copy_fail_warning() {
								echo "<div id='pxls-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
							}

							add_action( 'admin_notices', 'pxlsthemes_theme_update_copy_fail_warning' );
						}

						return;
					}

					
					function pxlsthemes_theme_updated_success() {
						echo "<div id='theme-upgraded' class='updated fade'><p>New theme successfully downloaded, extracted and updated.</p></div>";
					}
					add_action( 'admin_notices', 'pxlsthemes_theme_updated_success' );
				}
			}
		} //End user input save part of the update
	}
}

add_action( 'admin_head', 'pxlsthemes_theme_update_head' );


/* * *****************************************************************************
  PXLS Theme Version Getter
 * ***************************************************************************** */

function pxls_get_theme_version( $url = '', $check_if_critical = false ) {

	if ( !empty( $url ) ) {
		$theme_url = $url;
	}
	else {
		$theme_url = 'https://s3.amazonaws.com/pxls.co.uk/' . get_option( 'pxls_themename' ) . '-changelog.txt';
	}

	$theme_url = strtolower( $theme_url );

	$output = array( 'version' => '', 'is_critical' => false );

	$version_data = get_transient( 'pxlstheme_version_data' );

	if ( $version_data != '' && $check_if_critical == false ) {
		return $version_data;
	}

	$temp_file_addr = download_url( $theme_url );
	if ( !is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
		foreach ( $file_contents as $line_num => $line ) {
			$current_line = $line;

			if ( $line_num > 1 ) {

				if ( preg_match( '/^[0-9]/', $line ) ) {

					// Do critical update check.
					if ( $check_if_critical && ( strtolower( trim( substr( $line, -10 ) ) ) == 'critical' ) ) {
						$output['is_critical'] = true;
					}

					$current_line = stristr( $current_line, 'version' );
					$current_line = preg_replace( '~[^0-9,.]~', '', $current_line );
					$output['version'] = $current_line;
					break;
				}
			}
		}
		unlink( $temp_file_addr );
	}
	else {
		$theme_data = wp_get_theme();
		$output['version'] = $theme_data->Version;
	}

	// Set the transient containing the latest version number.
	set_transient( 'pxlstheme_version_data', $output, 60 * 60 * 24 );

	return $output;
}

// End pxls_get_fw_version()


/* * *****************************************************************************
  PXLS Theme Version Checker
 * ***************************************************************************** */

function pxls_theme_version_checker( $local_version, $check_if_critical = false ) {
	$data = array( 'is_update' => false, 'version' => '1.0.00', 'status' => 'none' );

	if ( !$local_version ) {
		return $data;
	}

	$version_data = pxls_get_theme_version( '', $check_if_critical );

	$check = version_compare( $version_data['version'], $local_version ); // Returns 1 if there is an update available.

	if ( $check == 1 ) {
		$data['is_update'] = true;
		$data['version'] = $version_data['version'];
		$data['is_critical'] = $version_data['is_critical'];
	}

	return $data;
}
// End pxls_theme_version_checker()


/**
 * pxls_footer_admin_text()
 * 
 * change the admin footer text
 * 
 * @since 0.01
 */
function pxls_footer_admin_text() {
    echo '<a href="http://pxls.co.uk/" target="_blank"><img src="' . trailingslashit( PXLS_FRAMEWORK_URI ) . 'images/pxls-slug.png" alt="PXLS:Themes" /></a>';
} 
add_filter( 'admin_footer_text', 'pxls_footer_admin_text', 9999 );



/**
 * pxls_footer_admin_version()
 * 
 * change the admin footer version text
 * 
 * @since 0.01
 */
function pxls_footer_admin_version() {
	return 'PXLS:Framework v' . PXLS_VERSION;
}
add_filter( 'update_footer', 'pxls_footer_admin_version', 9999 );