"""initial_schema

Revision ID: c1fcbd2346fc
Revises: 
Create Date: 2026-09-14 00:00:00.000000

"""
from alembic import op
import sqlalchemy as sa

revision = 'c1fcbd2346fc'
down_revision = None
branch_labels = None
depends_on = None


def upgrade() -> None:
    op.create_table(
        'users',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('username', sa.String(), nullable=False),
        sa.Column('email', sa.String(), nullable=True),
        sa.Column('telegram_id', sa.String(), nullable=True),
        sa.Column('hashed_password', sa.Text(), nullable=False),
        sa.Column('role', sa.String(), nullable=True, default='viewer'),
        sa.Column('is_active', sa.Boolean(), nullable=True, default=True),
        sa.Column('created_at', sa.DateTime(), nullable=True),
        sa.Column('updated_at', sa.DateTime(), nullable=True),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('username'),
        sa.UniqueConstraint('email'),
        sa.UniqueConstraint('telegram_id'),
    )
    op.create_index(op.f('ix_users_id'), 'users', ['id'], unique=False)
    op.create_index(op.f('ix_users_username'), 'users', ['username'], unique=True)
    op.create_index(op.f('ix_users_email'), 'users', ['email'], unique=True)
    op.create_index(op.f('ix_users_telegram_id'), 'users', ['telegram_id'], unique=True)

    op.create_table(
        'audit_logs',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('user_id', sa.Integer(), nullable=True),
        sa.Column('action', sa.String(), nullable=False),
        sa.Column('target_table', sa.String(), nullable=False),
        sa.Column('target_id', sa.Integer(), nullable=False),
        sa.Column('old_values', sa.Text(), nullable=True),
        sa.Column('new_values', sa.Text(), nullable=True),
        sa.Column('details', sa.Text(), nullable=True),
        sa.Column('ip_address', sa.String(), nullable=True),
        sa.Column('created_at', sa.DateTime(), nullable=True),
        sa.ForeignKeyConstraint(['user_id'], ['users.id'], ),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_audit_logs_id'), 'audit_logs', ['id'], unique=False)

    op.create_table(
        'otps',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('telegram_id', sa.String(), nullable=False),
        sa.Column('otp_code', sa.String(length=6), nullable=False),
        sa.Column('type', sa.String(), nullable=False),
        sa.Column('expires_at', sa.DateTime(), nullable=False),
        sa.Column('used', sa.Boolean(), nullable=True, default=False),
        sa.Column('created_at', sa.DateTime(), nullable=True),
        sa.PrimaryKeyConstraint('id')
    )
    op.create_index(op.f('ix_otps_id'), 'otps', ['id'], unique=False)
    op.create_index(op.f('ix_otps_telegram_id'), 'otps', ['telegram_id'], unique=False)

    op.create_table(
        'worker_configs',
        sa.Column('id', sa.Integer(), nullable=False),
        sa.Column('worker_name', sa.String(), nullable=False),
        sa.Column('interval_seconds', sa.Integer(), nullable=True, default=300),
        sa.Column('is_enabled', sa.Boolean(), nullable=True, default=True),
        sa.Column('last_run_at', sa.DateTime(), nullable=True),
        sa.Column('last_status', sa.String(), nullable=True, default='IDLE'),
        sa.Column('error_message', sa.Text(), nullable=True),
        sa.Column('updated_at', sa.DateTime(), nullable=True),
        sa.PrimaryKeyConstraint('id'),
        sa.UniqueConstraint('worker_name')
    )
    op.create_index(op.f('ix_worker_configs_id'), 'worker_configs', ['id'], unique=False)
    op.create_index(op.f('ix_worker_configs_worker_name'), 'worker_configs', ['worker_name'], unique=True)


def downgrade() -> None:
    op.drop_index(op.f('ix_worker_configs_worker_name'), table_name='worker_configs')
    op.drop_index(op.f('ix_worker_configs_id'), table_name='worker_configs')
    op.drop_table('worker_configs')
    op.drop_index(op.f('ix_otps_telegram_id'), table_name='otps')
    op.drop_index(op.f('ix_otps_id'), table_name='otps')
    op.drop_table('otps')
    op.drop_index(op.f('ix_audit_logs_id'), table_name='audit_logs')
    op.drop_table('audit_logs')
    op.drop_index(op.f('ix_users_telegram_id'), table_name='users')
    op.drop_index(op.f('ix_users_email'), table_name='users')
    op.drop_index(op.f('ix_users_username'), table_name='users')
    op.drop_index(op.f('ix_users_id'), table_name='users')
    op.drop_table('users')