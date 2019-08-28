<?php
/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_ProductQuestionsGraphQl
 * @copyright   Copyright (c) 2019 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */

namespace Ecomteck\ProductQuestionsGraphQl\Model\Resolver;

use Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface;
use Ecomteck\ProductQuestionsGraphQl\Model\Answers\CreateAnswer;
use Ecomteck\ProductQuestions\Model\UserType;
use Ecomteck\ProductQuestions\Model\Visibility;
use Ecomteck\ProductQuestions\Model\Status;
use Ecomteck\ProductQuestionsGraphQl\Model\Resolver\DataProvider\Answer as AnswerDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class SubmitAnswer
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Resolver
 */
class SubmitAnswer implements ResolverInterface
{
    /**
     * @var CreateAnswer
     */
    private $createAnswer;

    /**
     * User Type Model
     *
     * @var UserType
     */
    protected $userType;

    /**
     * @var AnswerDataProvider
     */
    private $answerDataProvider;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * SubmitQuestion constructor.
     * @param CreateAnswer $createAnswer
     * @param UserType $userType
     * @param AnswerDataProvider $answerDataProvider
     * @param QuestionRepositoryInterface $questionRepository
     */
    public function __construct(
        CreateAnswer $createAnswer,
        UserType $userType,
        AnswerDataProvider $answerDataProvider,
        QuestionRepositoryInterface $questionRepository
    ) {
        $this->createAnswer = $createAnswer;
        $this->userType = $userType;
        $this->answerDataProvider = $answerDataProvider;
        $this->questionRepository = $questionRepository;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return \Ecomteck\ProductQuestions\Api\Data\AnswerInterface|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $userCode = $this->userType->getGuestCode();

        $data = $this->validateQuestionInput($args);
        $data['question_id'] = $this->validateQuestion($args['input']['question_id']);
        $data['answer_status_id'] = Status::STATUS_PENDING;
        $data['answer_visibility_id'] = Visibility::VISIBILITY_NOT_VISIBLE;
        $data['answer_created_by'] = $userCode;
        $data['answer_user_type_id'] = $userCode;

        $answer = $this->createAnswer->execute($data);
        try {
            $answerData = $this->answerDataProvider->getData($answer->getId());
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $answerData;
    }

    /**
     * @param int $questionId
     * @return int
     * @throws GraphQlNoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateQuestion(int $questionId): int
    {
        try {
            $question = $this->questionRepository->getById($questionId);
            if (!$question->getId()) {
                throw new GraphQlNoSuchEntityException(
                    __("The question doesn't exist. Verify the question and try again.")
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $question->getId();
    }

    /**
     * @param array $args
     * @return array
     * @throws GraphQlInputException
     */
    private function validateQuestionInput(array $args): array
    {
        if (empty($args['input']['answer_detail'])) {
            throw new GraphQlInputException(__('Please enter the answer.'));
        }
        if (empty($args['input']['answer_author_name'])) {
            throw new GraphQlInputException(__('Please provide your name.'));
        }
        if (empty($args['input']['answer_author_email'])) {
            throw new GraphQlInputException(__('Please provide your email.'));
        }

        return [
                'answer_detail'       => $args['input']['answer_detail'],
                'answer_author_name'  => $args['input']['answer_author_name'],
                'answer_author_email' => $args['input']['answer_author_email']
            ];
    }
}
