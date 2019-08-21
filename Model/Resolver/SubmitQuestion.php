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

use Ecomteck\ProductQuestionsGraphQl\Model\Questions\CreateQuestion;
use Ecomteck\ProductQuestions\Model\UserType;
use Ecomteck\ProductQuestions\Model\Visibility;
use Ecomteck\ProductQuestions\Model\Status;
use Ecomteck\ProductQuestionsGraphQl\Model\Resolver\DataProvider\Question as QuestionDataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class SubmitQuestion
 * @package Ecomteck\ProductQuestionsGraphQl\Model\Resolver
 */
class SubmitQuestion implements ResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CreateQuestion
     */
    private $createQuestion;

    /**
     * User Type Model
     *
     * @var UserType
     */
    protected $userType;

    /**
     * @var QuestionDataProvider
     */
    private $questionDataProvider;

    /**
     * SubmitQuestion constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param CreateQuestion $createQuestion
     * @param UserType $userType
     * @param QuestionDataProvider $questionDataProvider
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CreateQuestion $createQuestion,
        UserType $userType,
        QuestionDataProvider $questionDataProvider
    ) {
        $this->productRepository = $productRepository;
        $this->createQuestion = $createQuestion;
        $this->userType = $userType;
        $this->questionDataProvider = $questionDataProvider;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return \Ecomteck\ProductQuestions\Api\Data\QuestionInterface|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $data = $this->validateQuestionInput($args);
        $data['product_id'] = $this->validateProduct($args['input']['product_id']);
        $data['question_visibility_id'] = Visibility::VISIBILITY_NOT_VISIBLE;
        $data['question_status_id'] = Status::STATUS_PENDING;
        $userCode = $this->userType->getGuestCode();
        $data['question_created_by'] = $userCode;
        $data['question_user_type_id'] = $userCode;

        $question = $this->createQuestion->execute($data);
        try {
            $questionData = $this->questionDataProvider->getData($question->getId());
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $questionData;
    }

    /**
     * @param int $productId
     * @return int
     * @throws GraphQlNoSuchEntityException
     */
    private function validateProduct(int $productId): int
    {
        try {
            $product = $this->productRepository->getById($productId);
            if (!$product->isVisibleInCatalog()) {
                throw new GraphQlNoSuchEntityException(
                    __("The product that was requested doesn't exist. Verify the product and try again.")
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $product->getId();
    }

    /**
     * @param array $args
     * @return array
     * @throws GraphQlInputException
     */
    private function validateQuestionInput(array $args): array
    {
        if (empty($args['input']['question_detail'])) {
            throw new GraphQlInputException(__('Please enter the question.'));
        }
        if (empty($args['input']['question_author_name'])) {
            throw new GraphQlInputException(__('Please provide your name.'));
        }
        if (empty($args['input']['question_author_email'])) {
            throw new GraphQlInputException(__('Please provide your email.'));
        }

        return [
                'question_detail'       => $args['input']['question_detail'],
                'question_author_name'  => $args['input']['question_author_name'],
                'question_author_email' => $args['input']['question_author_email']
            ];
    }
}
