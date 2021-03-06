<?php

/**
 * Custom file methods to get the X and Y coordinate
 */
file::$methods['focusX'] = function($file) {
  return focus::coordinates($file, 'x');
};

file::$methods['focusY'] = function($file) {
  return focus::coordinates($file, 'y');
};

/**
 * Custom file method 'focusCrop'
 */
file::$methods['focusCrop'] = function($file, $width, $height = null, $quality = null) {

  // don't scale thumbs further down
  if ($file->original()) {    
    throw new Exception('Thumbnails cannot be modified further');
  }
  
  $params = array();
  $params['width'] = $width;

  // if no height is given use width to crop a square
  $params['height'] = ($height) ? $height : $width;

  // determine aspect ratios
  $ratioSource = focus::ratio($file->width(), $file->height());
  $ratioThumb  = focus::ratio($params['width'], $params['height']);

  if ($ratioSource == $ratioThumb) {
    // no cropping, just resize 
    return $file->thumb($params);
  }

  if ($ratioThumb < $ratioSource) {
    $params['fit'] = 'height';
  } else {
    $params['fit'] = 'width';
  }
  
  $params['focus'] = TRUE;
  $params['ratio'] = $ratioThumb;

  // center as default focus
  $params['focusX'] = focus::coordinates($file, 'x');
  $params['focusY'] = focus::coordinates($file, 'y');

  $params['filename'] = '{safeName}-' . $params['width'] . 'x' . $params['height'] . '-' . $params['focusX']*100 . '-' . $params['focusY']*100 . '.{extension}';

  // quality set?
  if ($quality) $params['quality'] = $quality;
  
  // convert localized floats
  $params['ratio'] = focus::numberFormat($params['ratio']);
  $params['focusX'] = focus::numberFormat($params['focusX']);
  $params['focusY'] = focus::numberFormat($params['focusY']);

  return $file->thumb($params);
};