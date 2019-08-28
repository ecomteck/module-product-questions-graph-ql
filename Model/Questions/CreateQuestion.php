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

namespace Ecomteck\ProductQuestionsGraphQl\Model\Questions;

use Ecomteck\ProductQuestions\Api\Data\QuestionInterface;
use Ecomteck\ProductQuestions\Api\Data\QuestionInterfaceFactory;
use Ecomteck\ProductQuestions\Api\QuestionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CreateQuestion
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Questions
 */
class CreateQuestion
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var QuestionRepositoryInterface
     */
    private $questionRepository;

    /**
     * @var QuestionInterfaceFactory
     */
    private $questionFactory;

    /**
     * CreateQuestion constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param QuestionRepositoryInterface $questionRepository
     * @param QuestionInterfaceFactory $questionFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        QuestionRepositoryInterface $questionRepository,
        QuestionInterfaceFactory $questionFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->questionRepository = $questionRepository;
        $this->questionFactory = $questionFactory;
    }

    /**
     * @param array $args
     * @return QuestionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $args): QuestionInterface
    {
        $questionDataObject = $this->questionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $questionDataObject,
            $args,
            QuestionInterface::class
        );

        return $this->questionRepository->save($questionDataObject);
    }
}
