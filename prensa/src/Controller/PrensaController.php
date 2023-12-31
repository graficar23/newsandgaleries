<?php

namespace Drupal\prensa\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller to build the home.
 */
class PrensaController extends ControllerBase
{

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
    protected $entityTypeManager;

    /**
     * The entity query.
     *
     * @var \Drupal\Core\Entity\Query\QueryFactory
     */
    protected $entityQuery;



    public function __construct(EntityTypeManagerInterface $entityTypeManager, QueryFactory $entityQuery)
    {
        $this->entityTypeManager = $entityTypeManager;
        $this->entityQuery       = $entityQuery;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
      $container->get('entity_type.manager'),
      $container->get('entity.query')
    );
    }
    /* Método acción content devuelve directamente un contenido en html,
    este método será usado en una ruta */
    public function galeriaContent(){
      $galerias = [];

      $nids  = $this->traerGalerias();
      $nodes = $this->getNodeEntities($nids);

      foreach ($nodes as $key => $galeria) {
        
        $imagen = "";
        $descripcion = "";

        // $imagen = $this->urlImagenes($galeria->get('field_galerias_fotos'));
        $imagen = ImageStyle::load('imagen_noticias')->buildUrl($galeria->get('field_galerias_fotos')->get(0)->entity->uri->value);
        $descripcion = $galeria->get('body')->value;

        $galerias[$key] = [
          'url' => $this->getUrls($galeria->id()),
          'imagen' => $imagen,
          'title' => $galeria->getTitle(),
          'descripcion' =>  $descripcion,
          'created' => $this->getDates($galeria->get('created')->value),
        ];
      }
      
      $path = \Drupal::moduleHandler()->getModule('prensa')->getPath();

      return [
        '#theme' => 'galerias',
        '#galerias' => $galerias,
        '#url' => $path,
        '#pager' => [
          '#type' => 'pager',
        ],
      ];
    }

    public function videosContent(){
      $videos = [];

      $nids  = $this->traerVideos();
      $nodes = $this->getNodeEntities($nids);

      foreach ($nodes as $key => $video) {
        
        $imagen = "";
        $descripcion = "";
        
        // $imagen = $this->urlImagenes($video->get('field_videos_banner'));
        $imagen = ImageStyle::load('img_noticias_home')->buildUrl($video->get('field_videos_banner')->get(0)->entity->uri->value);
        $descripcion = $video->get('body')->value;

        $videos[$key] = [
          'url' => $video->get('field_videos_video')->video_id,
          'path' => $this->getUrls($video->id()),
          'imagen' => $imagen,
          'title' => $video->getTitle(),
          'descripcion' =>  $descripcion,
          'created' => $this->getDates($video->get('created')->value),
        ];
      }
      
      $path = \Drupal::moduleHandler()->getModule('prensa')->getPath();

      return [
        '#theme' => 'videos',
        '#videos' => $videos,
        '#url' => $path,
        '#pager' => [
          '#type' => 'pager',
        ],
      ];
    }

    public function audiosContent(){
      $audios = [];

      $nids  = $this->traerAudios();
      $nodes = $this->getNodeEntities($nids);

      foreach ($nodes as $key => $audio) {

        $descripcion = "";
        $descripcion = $audio->get('body')->value;

        $audios[$key] = [
          'url' => file_create_url($audio->get('field_audio_file')->entity->url()),
          'title' => $audio->getTitle(),
          'descripcion' =>  $descripcion,
          'created' => $this->getDates($audio->get('created')->value),
        ];
      }
      
      $path = \Drupal::moduleHandler()->getModule('prensa')->getPath();

      return [
        '#theme' => 'audios',
        '#audios' => $audios,
        '#url' => $path,
        '#pager' => [
          '#type' => 'pager',
        ],
      ];
    }

    private function traerGalerias(){

      $query = $this->entityQuery->get('node')
        ->condition('type', 'galerias')
        ->condition('status', '1')
        ->pager(12)
        ->sort('created', 'DESC')
        ->execute();
      return $query;

    }

    private function traerVideos(){

      $query = $this->entityQuery->get('node')
        ->condition('type', 'videos')
        ->condition('status', '1')
        ->pager(12)
        ->sort('created', 'DESC')
        ->execute();
      return $query;

    }

    private function traerAudios(){

      $query = $this->entityQuery->get('node')
        ->condition('type', 'audios')
        ->condition('status', '1')
        ->pager(12)
        ->sort('created', 'DESC')
        ->execute();
      return $query;

    }

    public function getNodeEntities($nids){
      return $this->entityTypeManager
        ->getStorage('node')
        ->loadMultiple($nids);
    }

    public function urlImagenes($value){
      if (sizeof($value) > 1) {
          $images = [];
          foreach ($value as $key => $image) {
              $images [] = file_create_url($image->entity->uri->value);
          }
          return $images;
      } else {
          $images = [];
          $value = $value->get(0)->entity;
          $images[] = file_create_url($value->getFileUri());
          return  $images;
      }
    }

    public function getValues($value){
      return  $value->get(0)->getValue();
    }

    public function getMulValues($value){
      return  $value->get(0)->getValue();
    }

    public function getUrls($nid){
      return \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$nid);
    }

    public function getDates($date){
      $dateP = \Drupal::service('date.formatter')->format($date, 'custom', 'Y-m-d\TH:i:s');
      $dateS = \Drupal::service('date.formatter')->format($date, 'custom', 'd \d\e F Y');
      return $dates = [
        'dateP' => $dateP,
        'dateS' => $dateS,
      ];
    }

}
