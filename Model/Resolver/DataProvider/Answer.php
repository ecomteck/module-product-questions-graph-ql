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

use Ecomteck\ProductQuestions\Api\Data\AnswerInterface;
use Ecomteck\ProductQuestions\Api\AnswerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Answer
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Resolver\DataProvider
 */
class Answer
{
    /**
     * @var AnswerRepositoryInterface
     */
    private $answerRepository;

    /**
     * Answer constructor.
     * @param AnswerRepositoryInterface $answerRepository
     */
    public function __construct(
        AnswerRepositoryInterface $answerRepository
    ) {
        $this->answerRepository = $answerRepository;
    }

    /**
     * @param int $answerId
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData(int $answerId): array
    {
        $answer = $this->answerRepository->getById($answerId);

        if (false === $answer->getAnswerStatusId()) {
            throw new NoSuchEntityException();
        }

        $answerData = [
            AnswerInterface::ANSWER_ID => $answer->getId(),
            AnswerInterface::ANSWER_DETAIL => $answer->getAnswerDetail(),
            AnswerInterface::ANSWER_AUTHOR_NAME => $answer->getAnswerAuthorName(),
            AnswerInterface::ANSWER_AUTHOR_EMAIL => $answer->getAnswerAuthorEmail(),
            AnswerInterface::QUESTION_ID => $answer->getQuestionId(),
            AnswerInterface::ANSWER_STATUS_ID => $answer->getAnswerStatusId(),
            AnswerInterface::ANSWER_USER_TYPE_ID => $answer->getAnswerUserTypeId(),
            AnswerInterface::ANSWER_USER_ID => $answer->getAnswerUserId(),
            AnswerInterface::ANSWER_CREATED_BY => $answer->getAnswerCreatedBy(),
            AnswerInterface::ANSWER_VISIBILITY_ID => $answer->getAnswerVisibilityId(),
            AnswerInterface::ANSWER_LIKES => $answer->getAnswerLikes(),
            AnswerInterface::ANSWER_DISLIKES => $answer->getAnswerDislikes(),
            AnswerInterface::ANSWER_CREATED_AT => $answer->getAnswerCreatedAt()
        ];
        return $answerData;
    }
}
