<?php

namespace Drupal\noticias\Controller;

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
class NoticiasController extends ControllerBase
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
    public function contenido(){
      $noticias = [];

      $nids  = $this->traerNoticias();
      $nodes = $this->getNodeEntities($nids);

      foreach ($nodes as $key => $noticia) {
        
            
        $imagen = "";
        $descripcion = "";

        
        // $imagen = $this->urlImagenes($noticia->get('field_noticias_imagen'));
        $imagen = ImageStyle::load('imagen_noticias')->buildUrl($noticia->get('field_noticias_imagen')->get(0)->entity->uri->value);
        $descripcion = $noticia->get('field_noticias_lead')->value;

        $noticias[$key] = [
          'url' => $this->getUrls($noticia->id()),
          'imagen' => $imagen,
          'title' => $noticia->getTitle(),
          'descripcion' =>  $descripcion,
          'created' => $this->getDates($noticia->get('created')->value),
        ];
      }
      
      $path = \Drupal::moduleHandler()->getModule('noticias')->getPath();

      return [
        '#theme' => 'noticias',
        '#noticias' => $noticias,
        '#url' => $path,
        '#pager' => [
          '#type' => 'pager',
        ],
      ];
    }

    private function traerNoticias(){

      $query = $this->entityQuery->get('node')
        ->condition('type', 'noticias')
        ->condition('status', '1')
        ->pager(6)
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
