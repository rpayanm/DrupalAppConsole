<?php
/**
 * @file
 * Containt Drupa\AppConsole\Command\Helper\FormTrait.
 */

namespace Drupal\AppConsole\Command\Helper;

use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait FormTrait
{
  /**
   * @param OutputInterface $output
   * @param HelperInterface $dialog
   * @return mixed
   */
  public function formQuestion(OutputInterface $output, HelperInterface $dialog)
  {
    if ($dialog->askConfirmation(
      $output,
      $dialog->getQuestion($this->trans('common.questions.inputs.confirm'), 'yes', '?'),
      true
    )) {
      $input_types = [
        'textfield',
        'textarea',
        'color',
        'date',
        'datetime',
        'email',
        'number',
        'range',
        'tel'
      ];

      $inputs = [];
      while (true) {
        // Label for input
        $input_label = $dialog->ask(
          $output,
          $dialog->getQuestion('  '.$this->trans('common.questions.inputs.label'),'',':'),
          null
        );

        if (empty($input_label)) {
          break;
        }

        // Machine name
        $input_machine_name = $this->getStringUtils()->createMachineName($input_label);

        $input_name = $dialog->ask(
          $output,
          $dialog->getQuestion('  '.$this->trans('common.questions.inputs.machine_name'), $input_machine_name, ':'),
          $input_machine_name
        );

        // Type input
        $input_type = $dialog->askAndValidate(
          $output,
          $dialog->getQuestion('  '.$this->trans('common.questions.inputs.type'), 'textfield',':'),
          function ($input) use ($input_types) {
            if (!in_array($input, $input_types)) {
              throw new \InvalidArgumentException(
                sprintf($this->trans('common.questions.inputs.invalid'), $input)
              );
            }

            return $input;
          },
          false,
          'textfield',
          $input_types
        );

        array_push($inputs, array(
          'name'  => $input_name,
          'type'  => $input_type,
          'label' => $input_label
        ));
      }

      return $inputs;
    }
    return null;
  }
}
