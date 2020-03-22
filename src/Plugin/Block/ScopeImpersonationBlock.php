<?php
/**
 * @file
 * Contains Drupal\scope_impersonation\Plugin\Block\ScopeImpersonationBlock.
 */

namespace Drupal\scope_impersonation\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\Role;
use Drupal;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\scope_impersonation\Form\ScopeImpersonationForm;

/**
 * Provides a block with options to switch scope.
 *
 * @Block(
 *   id = "scope_impersonation_block",
 *   admin_label = @Translation("Switch Scope")
 * )
 */
class ScopeImpersonationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $form = \Drupal::formBuilder()->getForm('Drupal\scope_impersonation\Form\ScopeImpersonationForm');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    // return $account->hasPermission('administer permissions');
    if ( AccessResult::allowedIfHasPermission($account, 'view scope impersonation') ) {
      return AccessResult::allowedIfHasPermission($account, 'view scope impersonation');
    }
  }
}
