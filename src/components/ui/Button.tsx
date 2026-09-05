import React from "react";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: "primary" | "secondary" | "danger";
  size?: "sm" | "md" | "lg";
  children: React.ReactNode;
}

export const Button: React.FC<ButtonProps> = ({
  variant = "primary",
  size = "md",
  className = "",
  children,
  ...props
}) => {
  const sizeStyles: Record<string, React.CSSProperties> = {
    sm: { padding: "8px 16px", fontSize: "0.875rem" },
    md: { padding: "12px 24px", fontSize: "1rem" },
    lg: { padding: "16px 32px", fontSize: "1.125rem" },
  };

  return (
    <button
      className={`btn btn-${variant} ${className}`}
      style={sizeStyles[size]}
      {...props}
    >
      {children}
    </button>
  );
};
