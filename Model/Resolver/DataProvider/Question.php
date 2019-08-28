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
declare(strict_types=1);

namespace Ecomteck\ProductQuestionsGraphQl\Model\Resolver\DataProvider;

use Ecomteck\ProductQuestions\Api\Data\QuestionInterface;
use Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Product question data provider
 */
class Question
{
    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @param QuestionRepositoryInterface $questionRepository
     */
    public function __construct(
        QuestionRepositoryInterface $questionRepository
    ) {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @param int $questionId
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData(int $questionId): array
    {
        $question = $this->questionRepository->getById($questionId);

        if (false === $question->getQuestionStatusId()) {
            throw new NoSuchEntityException();
        }

        $questionData = [
            QuestionInterface::QUESTION_ID => $question->getId(),
            QuestionInterface::QUESTION_DETAIL => $question->getQuestionDetail(),
            QuestionInterface::QUESTION_AUTHOR_NAME => $question->getQuestionAuthorName(),
            QuestionInterface::QUESTION_AUTHOR_EMAIL => $question->getQuestionAuthorEmail(),
            QuestionInterface::QUESTION_STATUS_ID => $question->getQuestionStatusId(),
            QuestionInterface::QUESTION_USER_TYPE_ID => $question->getQuestionUserTypeId(),
            QuestionInterface::CUSTOMER_ID => $question->getCustomerId(),
            QuestionInterface::QUESTION_VISIBILITY_ID => $question->getQuestionVisibilityId(),
            QuestionInterface::QUESTION_STORE_ID => $question->getQuestionStoreId(),
            QuestionInterface::QUESTION_LIKES => $question->getQuestionLikes(),
            QuestionInterface::QUESTION_DISLIKES => $question->getQuestionDislikes(),
            QuestionInterface::TOTAL_ANSWERS => $question->getTotalAnswers(),
            QuestionInterface::PENDING_ANSWERS => $question->getPendingAnswers(),
            QuestionInterface::PRODUCT_ID => $question->getProductId(),
            QuestionInterface::QUESTION_CREATED_BY => $question->getQuestionCreatedBy(),
            QuestionInterface::QUESTION_CREATED_AT => $question->getQuestionCreatedAt()
        ];
        return $questionData;
    }
}
