<?php

/**
*	@brief Generate album cover filename
*	@param $row Array where must be keys ZALBUMARTISTSNAMES and ZTITLE
*	@return String
**/
function get_album_cover_filename( $row )
{
	$filename = $row['ZALBUMARTISTSNAMES'] . '-' . $row['ZTITLE'];
	$filename = str_replace( ' ', '_', $filename );
	$filename = str_replace( '/', '_', $filename );
	$filename = str_replace( 'ä', 'a', $filename );
	$filename = str_replace( 'ö', 'o', $filename );
	$filename = str_replace( '?', '_', $filename );
	$filename .= '.jpg';
	$filename = strtolower( $filename );

	return $filename;
}

?>
