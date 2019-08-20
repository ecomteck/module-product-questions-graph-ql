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

namespace Ecomteck\ProductQuestionsGraphQl\Model\Answers;

use Ecomteck\ProductQuestions\Api\Data\AnswerInterface;
use Ecomteck\ProductQuestions\Api\Data\AnswerInterfaceFactory;
use Ecomteck\ProductQuestions\Api\AnswerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CreateAnswer
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Answers
 */
class CreateAnswer
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AnswerRepositoryInterface
     */
    private $answerRepository;

    /**
     * @var AnswerInterfaceFactory
     */
    private $answerFactory;

    /**
     * CreateAnswer constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param AnswerRepositoryInterface $answerRepository
     * @param AnswerInterfaceFactory $answerFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        AnswerRepositoryInterface $answerRepository,
        AnswerInterfaceFactory $answerFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->answerRepository = $answerRepository;
        $this->answerFactory = $answerFactory;
    }

    /**
     * @param array $args
     * @return AnswerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(array $args): AnswerInterface
    {
        $answerDataObject = $this->answerFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $answerDataObject,
            $args,
            AnswerInterface::class
        );

        return $this->answerRepository->save($answerDataObject);
    }
}
