<?php

/**
 * @file
 */

namespace Drupal\geocoder_geofield\Geocoder\Dumper;

use Geocoder\Dumper\Dumper;
use Geocoder\Model\Address;
use Drupal\geophp\geoPHPInterface;

/**
 * @author Pol Dellaiera <pol.dellaiera@gmail.com>
 */
class Geohash implements Dumper {
  /**
   * @var \Geocoder\Dumper\Dumper
   */
  protected $dumper;

  /**
   * @var \Drupal\geophp\geoPHPInterface
   */
  protected $geophp;

  /**
   * @inheritdoc
   */
  public function dump(Address $address) {
    return $this->geophp->load($this->dumper->dump($address), 'json')->out('geohash');
  }

  /**
   * @inheritDoc
   */
  public function __construct(Dumper $dumper, geoPHPInterface $geophp) {
    $this->dumper = $dumper;
    $this->geophp = $geophp;
  }

}
