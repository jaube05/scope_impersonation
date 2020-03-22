<?php

namespace Drupal\scope_impersonation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Access\AccessResult;

class ScopeImpersonationController extends ControllerBase {


  public function switchScope($rid, Request $request) {
    // Redirect to the front page if destination does not exist.
    $destination = $request->get('destination');
    $url = empty($destination) ? '/' : $destination;

    //If user selected 'reset' in selection list
    if ($rid == 'reset') {

      scope_impersonation_user_scope_reset();
      $_SESSION['impersonation_scope']='reset';
      return new RedirectResponse($url);
    }
    scope_impersonation_user_scope_switch($rid);

    return new RedirectResponse($url);

}
    /**
    * Checks access for this controller.
    */
    public function access() {
      return AccessResult::allowed();
    }
}
