<?php
/**
 * @file
 * Contains \Drupal\scope_impersonation\Form\ScopeImpersonationForm.
 */
namespace Drupal\scope_impersonation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal;
use Drupal\user\Entity\Role;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\UserInterface;

class ScopeImpersonationForm extends FormBase {
    // protected $listOfRoles;
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'scope_impersonation_role_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $options = array(
            'query' => drupal_get_destination()
        );
        $linkGenerator = Drupal::linkGenerator();

        //Add placeholder
        $listOfScope[] = "Scope";
        $urlReset = "";

        //Gets list of all scope with Business Unit
        $connection = \Drupal::database();

        /** @var AccountProxyInterface */
        $sessionUser = \Drupal::currentUser();

        // /** @var UserInterface $user */
        $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

        // Get original scope link to reset back to .
        foreach($user->get('field_scope_raw')->getValue() as $scope) {
            $urlReset = Url::fromRoute('scope_impersonation.switchscope', array('rid' => 'reset'), $options);
            $urlReset = $urlReset->toString();

        }
        $lang_code = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $query = $connection->query("SELECT tid, name FROM taxonomy_term_field_data tx INNER JOIN taxonomy_term__field_scope_type sc ON sc.entity_id = tx.tid WHERE sc.field_scope_type_value = 'Business Unit' AND tx.langcode = 'en' ORDER BY tx.name");
        $result = $query->fetchAll();
        $count = 0;
        foreach ($result as $record) {
            $url = Url::fromRoute('scope_impersonation.switchscope', array('rid' => $record->tid), $options);
            $taxonomy_term = \Drupal\taxonomy\Entity\Term::load($record->tid);
            $taxonomy_term_trans = \Drupal::service('entity.repository')->getTranslationFromContext($taxonomy_term, $lang_code)->get("name")[0]->getValue()['value'];
            $url = $url->toString();
            if ($urlReset === $url) {
                $listOfScope[$url] = 'Reset';
                $count++;
            } else {
                $listOfScope[$url] = $taxonomy_term_trans;
            }
        }

        if ($count === 0){
            $listOfScope[$urlReset] = 'Reset';
        }
        $urlBUDefault = 0;

        if(isset($_SESSION['impersonation_scope'])){
            if($_SESSION['impersonation_scope'] == 'reset'){
                // This will revert the select list back to 'Impersonate Scope'
            }
            else {
                //Gets the session url to save in the selected form list
                $url = Url::fromRoute('scope_impersonation.switchscope', array('rid' => $_SESSION['impersonation_scope']), $options);
                $urlBUDefault = $url->toString();
            }

        }



        $form['role_scope_impersonation'] = array(
            '#type' => 'select',
            '#options' => $listOfScope,
            '#attributes' => array('onchange' => 'document.getElementById("scope-impersonation-role-form").action = this.value; this.form.submit();'),
            '#default_value' => $urlBUDefault,
        );
        $form['#scope-impersonation-role-form']['#attributes']['placeholder'] = 'Scope';


        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        //Not needed as the selected form will auto submit selection
    }
}
