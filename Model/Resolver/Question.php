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

namespace Ecomteck\ProductQuestionsGraphQl\Model\Resolver;

use Ecomteck\ProductQuestionsGraphQl\Model\Resolver\DataProvider\Question as QuestionDataProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Question
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Resolver
 */
class Question implements ResolverInterface
{
    /**
     * @var QuestionDataProvider
     */
    private $questionDataProvider;

    /**
     * Question constructor.
     * @param QuestionDataProvider $questionDataProvider
     */
    public function __construct(QuestionDataProvider $questionDataProvider)
    {
        $this->questionDataProvider = $questionDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $questionId = $this->getQuestionId($args);
        $questionData = $this->getQuestionData($questionId);

        return $questionData;
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getQuestionId(array $args): int
    {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"Question id should be specified'));
        }

        return (int)$args['id'];
    }

    /**
     * @param int $questionId
     * @return array
     * @throws GraphQlNoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuestionData(int $questionId): array
    {
        try {
            $questionData = $this->questionDataProvider->getData($questionId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $questionData;
    }
}
