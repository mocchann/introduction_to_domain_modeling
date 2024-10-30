<?php

namespace Repository\Users;

use Builder\Users\UserDataModelBuilder;
use DomainObject\Entity\Users\User;

class EFUserRepository implements IUserRepository
{
    public function save(User $user): void
    {
        // 通知オブジェクトを引き渡して内部データを取得
        $user_data_model_builder = new UserDataModelBuilder($user);
        $user->notify($user_data_model_builder);

        // 通知された内部データからデータモデルを生成
        $user_data_model = $user_data_model_builder->build();

        // データモデルをORMに引き渡して保存
        // ...略
    }
}
